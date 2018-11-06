<?php
/**
* @file
* Contains the theme's functions to manipulate Drupal's default markup.
*
* Complete documentation for this file is available online.
* @see https://drupal.org/node/1728096
*/


/**
* Override or insert variables into the maintenance page template.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("maintenance_page" in this case.)
*/
/* -- Delete this line if you want to use this function
function govcms_darp_preprocess_maintenance_page(&$variables, $hook) {
// When a variable is manipulated or added in preprocess_html or
// preprocess_page, that same work is probably needed for the maintenance page
// as well, so we can just re-use those functions to do that work here.
govcms_darp_preprocess_html($variables, $hook);
govcms_darp_preprocess_page($variables, $hook);
}
// */

/**
* Override or insert variables into the html templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("html" in this case.)
*/

function govcms_darp_preprocess_html(&$variables, $hook) {
  if ($node = menu_get_object()) {
    // For annual report content, lookup the current page's field_entity term
    // reference and add as a body class
    if ($field_entity = field_get_items('node', $node, 'field_entity')) {
      if ($term = taxonomy_term_load($field_entity[0]['tid'])) {
        $variables['classes_array'][] = 'entity__' . drupal_html_class($term->name);
      }
    }
  }
}


/**
* Override or insert variables into the page templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("page" in this case.)
*/

function govcms_darp_preprocess_page(&$variables, $hook) {
  // ddl($variables);
  // Replace the PNG logo with a SVG one
  $variables['logo'] = str_replace('logo.png', 'logo.svg', $variables['logo']);
}


/**
* Override or insert variables into the node templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("node" in this case.)
*/

function govcms_darp_preprocess_node(&$variables, $hook) {
  // Optionally, run node-type-specific preprocess functions, like
  // govcms_darp_preprocess_node_page() or
  // govcms_darp_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }

  // If the page is empty skip to the next one as referenced by the 'next page' field
  if (govcms_darp_is_body_empty($variables['node'])) {
    // Get the nid of the next page in the publication and redirect
    if ($next_nid = field_get_items('node', $variables['node'], 'field_next_page')) {
      drupal_goto('node/' . $next_nid[0]['target_id']);
    }
  }
}


/**
* Override or insert variables into the comment templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("comment" in this case.)
*/
/* -- Delete this line if you want to use this function
function govcms_darp_preprocess_comment(&$variables, $hook) {
$variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
* Override or insert variables into the region templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("region" in this case.)
*/
/* -- Delete this line if you want to use this function
function govcms_darp_preprocess_region(&$variables, $hook) {
// Don't use Zen's region--sidebar.tpl.php template for sidebars.
//if (strpos($variables['region'], 'sidebar_') === 0) {
//  $variables['theme_hook_suggestions'] =
// array_diff($variables['theme_hook_suggestions'], array('region__sidebar'));
//}
}
// */

/**
* Override or insert variables into the block templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("block" in this case.)
*/

function govcms_darp_preprocess_block(&$variables, $hook) {
  if ($variables['block_html_id'] === 'block-block-1') {
    $variables['classes_array'][] = 'block-ar-tree';

    // Get the current node
    if ($node = menu_get_object()) {
      // Load the view to get all pages in the current annual report
      $view = views_get_view('annual_report_tree');
      if ($node->type == 'annual_report_page') {
        $view->set_display('block_1');
      } else {
        $view->set_display('block_2');
      }
      $view->set_arguments(array($node->nid));
      $view->execute();

      // Convert the results to an item list
      $outArray = array();
      report_menu_list($view->result, $outArray);

      // Add the list to the content
      $attributes = array(
        'id' => 'ar-nav'
      );
      $variables['content'] = theme_item_list(array(
        'items' => $outArray,
        'title' => false,
        'type' => 'ul',
        'attributes' => $attributes
      ));
    }
  }
}


/**
 * Implement hook_preprocess_field().
 */
function govcms_darp_preprocess_field(&$variables) {
  // Get the field element.
  $element = $variables['element'];

  // Process the body field
  if ($element['#field_name'] == 'body') {
    $items = $element['#items'];
    // Looping on field items.
    foreach ($items as $delta => $item) {
      if (!empty($variables['items'][$delta]['#markup'])) {
        $content = $variables['items'][$delta]['#markup'];
        // Process any imported images
        $content = govcms_darp_md_image_path($content);
        // Process imported links
        $content = govcms_darp_md_link_path($content);
        // Apply token replace function to updated content.
        $variables['items'][$delta]['#markup'] = token_replace($content);
      }
    }
  }
}


function govcms_darp_md_image_path($text) {
  // Return the text as quick as possible.
  if (FALSE === strpos($text, 'IMG_TOKEN_REPLACE')) {
    return $text;
  }

  // Find all imported images
  preg_match_all('@/md_images/([^/]+)/([^\?]+)\?itok=(IMG_TOKEN_REPLACE)@', $text, $matches);

  // Return the text if no matches.
  if (empty($matches) || !isset($matches[0])) {
    return $text;
  }

  // Generate the correct path and image style token
  $replaced_path = [];
  foreach ($matches[0] as $key => $image) {
    $token = image_style_path_token($matches[1][$key], 'public://' . $matches[2][$key]);
    $replaced_path[] = '/' . variable_get('file_public_path', conf_path() . '/files') . '/styles/' . $matches[1][$key] . '/public/' . $matches[2][$key] . '?itok=' . $token;
  }

  // Replace each image path using the correct directory and image style token
  $text = str_replace($matches[0], $replaced_path, $text);

  return $text;
}


function govcms_darp_md_link_path($text) {
  // Return the text as quick as possible.
  if (FALSE === strpos($text, 'md_link')) {
    return $text;
  }

  // Find all imported links
  preg_match_all('@/md_link/([a-fA-F0-9\-]+)@', $text, $matches);

  // Return the text if no matches.
  if (empty($matches) || !isset($matches[0])) {
    return $text;
  }

  // Generate the correct url for the link based on the imported UUID
  $replaced_path = [];
  foreach ($matches[0] as $key => $link) {
    $query = new EntityFieldQuery();
    $result = $query
      ->entityCondition('entity_type', 'node')
      ->fieldCondition('field_uuid', 'value', $matches[1][$key], '=')
      ->propertyCondition('status', NODE_PUBLISHED)
      ->execute();

    if (!empty($result['node'])) {
      $url = url('node/' . current($result['node'])->nid);
      $replaced_path[] = $url;
    }
  }

  // Replace each link URL
  $text = str_replace($matches[0], $replaced_path, $text);

  return $text;
}


/**
* Implements hook_form_alter()
*/
function govcms_darp_form_alter(&$form, &$form_state, $form_id) {
  // Add placeholder text to the annual report search filter
  // Change default selected option text to the annual report select filter
  if ($form_id == 'views_exposed_form' && $form['#id'] == 'views-exposed-form-annual-reports-block') {
    $form['field_entity_tid']['#attributes']['placeholder'] = t('Find a Department or Agency...');
    $form['field_portfolio_tid']['#options']['All'] = t('Portfolios');
  }
}


/**
 * Recursive function to generate an array representing a reports page structure
 * from a view results array
 *
 * @param $inArray
 *   The result object from a view.
 * @param $outArray
 *   Array to pass to theme_item_list
 * @param $currentParentId
 *   Id of the current items parent to use in recursion
 */
function report_menu_list(&$inArray, &$outArray, $currentParentId = 0) {
  if(!is_array($inArray)) {
    return;
  }

  if(!is_array($outArray)) {
    return;
  }

  foreach($inArray as $key => $item) {
    $parent_id = false;
    if (!empty($item->field_field_parent)) {
      $parent_id = $item->field_field_parent[0]['raw']['target_id'];
    }
    $item_id = $item->field_annual_report_node_nid;
    $title = $item->field_annual_report_node_title;

    $output = array('data' => l($title, 'node/' . $item_id));

    if($parent_id == $currentParentId) {
      $children = array();
      report_menu_list($inArray, $children, $item_id);
      if (!empty($children)) {
        $output['children'] = $children;
      }
      $outArray[] = $output;
    }
  }
}


/**
 * Check if the body of a given node is empty.
 *
 * @param object $node
 *   The node to check.
 *
 * @return boolean
 *   Whether or not the node has body text.
 */
function govcms_darp_is_body_empty($node) {
  if ($node->status == 0 || !node_access('view', $node)) {
    return TRUE;
  }
  $body = field_get_items('node', $node, 'body');
  if (!$body || trim($body[0]['value']) == '') {
    return TRUE;
  }
  else {
    return FALSE;
  }
}
