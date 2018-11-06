/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function ($, Drupal, window, document, undefined) {

  $(document).ready(function() {
    menuExpand();
    expandCollapse();
    offscreenMenu();
    objectFit();
    matchHeight();
    activeMenu();
    darpShareThis();
    footerContent();
    tabIndex();
    ariaRoles();
  });


/**
* Add expand/collapse to content menu
*/
function menuExpand() {
  // Create the collapse button
  $('#ar-nav > li').has('ul').each(function(index) {
    $(this).children('a').after( '<span class="menu-collapser"></span>' );
    $('.menu-collapser').attr({ tabindex: '0', title: "Press [enter] to view list of contents", role: 'button'});
    $(this).attr("aria-expanded", "false");
  });

  // Do the collapse on click
  $('#ar-nav > li > .menu-collapser').click(function(e) {
    console.log(this);
    expandCollapse(this);
  });

  // Do the collapse on 'enter'
  $('#ar-nav > li > .menu-collapser').keypress(function(e) {
    if(e.which == 13) {
      expandCollapse(this);
    }
  });
}

/**
* Expandable menu collapser toggle
*/
function expandCollapse(target) {
  $(target).closest('li').toggleClass( 'menu-collapser-collapsed' );
  if ($(target).closest('li').hasClass("menu-collapser-collapsed")) {
    // It's open
    $(target).closest('li').attr("aria-expanded", "true");
  } else {
    $(target).closest('li').attr("aria-expanded", "false");
    $('#ar-nav li .item-list *').attr("aria-hidden", "true");
  }
}

/**
* Finds if a child menu item is active and applys classes to parent
*/
function activeMenu() {
  $('#ar-nav ul li > a.active').parents('li').addClass('active-parent-expand');
  $('.active-parent-expand').addClass('menu-collapser-collapsed').attr("aria-expanded", "true");
}

/**
* Offscreen menu sidebar
*/
function offscreenMenu() {
  $('#offscreen-menu').click(function(){
    $('body').toggleClass('menu-open');
  });
}

/**
* For browsers that don't support CSS object-fit (including IE)
*/
function objectFit() {
  if('objectFit' in document.documentElement.style === false) {
    $('.views-field-field-banner-image .field-content').not('.img-cover-processed').each(function() {
      var $img = $(this).find('img').first();
      //adds a new wrapper around the image
      var $wrapper = $('<div class="img-cover-wrapper"></div>');
      //changes inline image to background image
      $wrapper.css('background-image', 'url(' + $img.attr('src') + ')');
      $(this).wrapInner($wrapper);
      //adds class name for styling
      $(this).addClass('img-cover-processed');
    });
  }
}

/**
* Selection sharing: share-this
*/
function darpShareThis() {
  $('#content').once('sharethis', function() {
    if (Drupal.selectionShare) Drupal.selectionShare.destroy();
    var shareThis = window.ShareThis;
    Drupal.selectionShare = shareThis({
      selector: '#content',
      sharers: [
        window.ShareThisViaTwitter,
        window.ShareThisViaFacebook,
        window.ShareThisViaLinkedIn,
        window.ShareThisViaEmail
      ]
    });
    if (!window.matchMedia || !window.matchMedia("(pointer: coarse)").matches) {
      if (Function('/*@cc_on return document.documentMode===10@*/')()){
        // Disable in IE10
      } else {
        Drupal.selectionShare.init();
      }
    }
  });
}

/**
* Alternative to flexbox
*/
function matchHeight() {
  $('.view-next-previous-navigation .views-field').matchHeight();
}

/**
* Annual report page content paramaters
*/
function footerContent() {
  if ( $('.group-footer, .group-left').children().length == 0) {
     $('.group-footer, .group-left').addClass('empty-div');
  }
}

/**
* Change tab index (not working)
*/
function tabIndex() {
  $('a.at-share-btn').attr("tabindex", -1);
}

/**
* Add aria roles
*/
function ariaRoles() {
  $('.view-annual-reports > .view-content').attr("role", "list");
  $('.view-annual-reports > .view-content > .item-list').attr("role", "listitem");
}

/**
* Accessibility fixes
*/
Drupal.behaviors.dfata_accessibilityFixes = {
  attach: function(context, settings) {

    // replace epmty alt attribute with with blank alt text
    $('img[alt]').attr("alt", " ");

    // add blank alt text to all banner images
    $('.view-annual-report-header .views-field-field-banner-image img').attr("alt", " ");
  }
};

/**
* Responsive tables
*/
Drupal.behaviors.psaResponsiveTables = {
    attach: function(context, settings) {

      // Create the wrapper div
      $('table.table').each(function(index) {
        $(this).wrap( '<div class="table-responsive"></div>' );
      });

      // Resizing the window adds or removes table wrapper class
      $( window ).resize(function() {

      // Get the width of the wrapper
      var containerWidth = $('.table-responsive').innerWidth();

      // $('.table').removeClass('large, small') ;
        $('.table').each(function() {
          $(this).parent().removeClass('large, small') ;
            // Get the width of the table and add a class depending on the size
            var tableWidth = $(this).width();
            // console.log(containerWidth);
            // console.log(tableWidth);
            if (tableWidth < containerWidth) {
                $(this).parent().addClass('large');
            } else {
                $(this).parent().addClass('small');
            }
        });
      });
    }
  };

})(jQuery, Drupal, this, this.document);
