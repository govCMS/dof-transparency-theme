<?php
/**
 * @file
 * Returns HTML for a sidebar region.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728118
 */
?>
<?php if ($content): ?>
  <section class="<?php print $classes; ?>">
    <?php print $content; ?>
    <div>
      <!-- Go to www.addthis.com/dashboard to customize your tools -->
      <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5ba439321f006bf7"></script>
    </div>
  </section>
<?php endif; ?>
