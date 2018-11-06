<?php
/**
 * @file
 * Returns the HTML for a single Drupal page.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728148
 */
?>
<header id="mobile-header">
  <?php if ($logo): ?>
    <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="header__logo" id="logo">
      <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="header__logo-image" />
      <?php if ($is_front): ?>
        <h1 class="site-name">Transparency <em>Portal</em></h1>
      <?php else: ?>
        <h2 class="site-name">Transparency <em>Portal</em></h2>
      <?php endif; ?>
    </a>
  <?php endif; ?>
  <button tabindex="2" id="offscreen-menu" class="hamburger hamburger--collapse" type="button">
    <span class="hamburger-box">
      <span class="hamburger-inner"></span>
    </span>
  </button>
</header>

<header class="header" id="header" role="banner">
  <div class="header__inner">

    <?php if ($logo): ?>
      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" class="header__logo" id="logo">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" class="header__logo-image" />
        <?php if ($is_front): ?>
          <h1 class="site-name">Transparency <em>Portal</em></h1>
        <?php else: ?>
          <h2 class="site-name">Transparency <em>Portal</em></h2>
        <?php endif; ?>
      </a>
    <?php endif; ?>


    <?php print render($page['navigation']); ?>

    <?php if ($main_menu): ?>
      <nav class="header__main-menu" id="main-menu" role="navigation">

        <?php print theme('links__system_main_menu', array(
          'links' => $main_menu,
          'attributes' => array(
            'class' => array(
              'links',
              'clearfix',
            ),
          ),
          'heading' => array(
            'text' => isset($main_menu_heading) ? $main_menu_heading : '',
            'level' => 'h2',
            'class' => array('element-invisible'),
          ),
        )); ?>
      </nav>
    <?php endif; ?>
    <nav class="header__tools-menu" id="tools-menu" role="navigation">
      <?php
      $menu = menu_navigation_links('menu-tools-menu');
        print theme('links__menu_secondary_menu_logged_out', array(
             	'links' => $menu,
          	'attributes' => array(
  	        	'id' => 'tools-menu-links',
  	        	'class' => array('links', 'clearfix'),
  	          ),
  	      	'heading' => array(
  	        	'text' => 'TOOLS',
  	        	'level' => 'h2',
  	          ),
        ));
      ?>
    </nav>

    <?php print render($page['header']); ?>

    <?php if ($secondary_menu): ?>
      <nav class="header__secondary-menu" id="secondary-menu" role="navigation">
        <?php print theme('links__system_secondary_menu', array(
          'links' => $secondary_menu,
          'attributes' => array(
            'class' => array(
              'links',
              'clearfix',
            ),
          ),
          'heading' => array(
            'text' => isset($secondary_menu_heading) ? $secondary_menu_heading : '',
            'level' => 'h2',
            'class' => array('element-invisible'),
          ),
        )); ?>
      </nav>
    <?php endif; ?>
  </div>

</header>


<div id="page">


  <?php print render($page['highlighted']); ?>

  <!-- <?php print $breadcrumb; ?> -->

  <div id="main" class="clearfix">
    <div id="content" class="column" role="main">
      <a href="#skip-link" id="skip-content" class="element-invisible">Go to top of page</a>

      <div class="comparison-tool"
        side-menu-width="0"
        entity="7ef1ec0b-b2bc-5147-a21d-aadf67fc5116"
        year="2017/18"
        type="Annual report">
      </div>
      <script type="text/javascript" src="/sites/all/themes/govcms_darp/js/compare/index-bundle.js"></script>
    </div>
    <?php
      // Render the sidebars to see if there's anything in them.
      $sidebar_first  = render($page['sidebar_first']);
      $sidebar_second = render($page['sidebar_second']);
    ?>

    <?php if ($sidebar_first || $sidebar_second): ?>
      <aside class="sidebars" role="complementary">
        <?php print $sidebar_first; ?>
        <?php print $sidebar_second; ?>
      </aside>
    <?php endif; ?>

  </div>

  <?php print render($page['footer']); ?>

</div>

<?php print render($page['bottom']); ?>
