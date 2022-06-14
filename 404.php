<?php

/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package gioia
 */
get_header();
?>

<div class="container">
  <div class="not-found">
    <h3 class="not-found__text">404</h3>
    <div class="not-found__desc">
      <p><?php _e('It seems you are lost.', 'gioia'); ?></p>
      <div class="wp-block-button">
        <a href="<?= esc_url(home_url('/')); ?>" class="wp-block-button__link"><?php _e('Back to home', 'gioia'); ?></a>
      </div>
    </div>
  </div>
</div>

<?php
get_footer();
