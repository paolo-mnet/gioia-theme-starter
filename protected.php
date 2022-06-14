<?php

/**
 * The template for displaying password protected post by default.
 *
 * @link https://wordpress.org/support/article/using-password-protection/
 *
 * @package gioia
 */
get_header();
$post_id = get_the_ID() ?: rand();
?>

<div class="container">
  <div class="post-protected">
    <form class="wpcf7-form post-protected__form" action="<?= esc_url(site_url('wp-login.php?action=postpass', 'login_post')); ?>" class="wpcf7-form" method="post">
      <p><?php _e('This content is password protected. To view it please enter your password below:'); ?></p>
      <div class="wpcf7-form-group">
        <label for="<?= esc_attr("pwbox-{$post_id}") ?>" class="sr-only"><?php _e('Password:'); ?></label>
        <input name="post_password" id="<?= esc_attr("pwbox-{$post_id}") ?>" type="password" size="20" placeholder="<?= esc_attr_x('Password', 'post password form'); ?>" />
      </div>
      <input type="submit" class="btn btn-primary" name="<?= __('Submit'); ?>" value="<?= esc_attr_x('Enter', 'post password form'); ?>" />
    </form>
  </div>
</div>

<?php
get_footer();
