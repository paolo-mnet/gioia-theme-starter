<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Theme-based language selector with Polylang.
 *
 * @method gioia_languages
 */
if (!function_exists('gioia_languages')) {
  function gioia_languages($echo = true) {
    $curr_lang = pll_current_language('slug');
    ob_start();
?>
    <div class="pll-selector">
      <span class="pll-selector__label" title="<?php esc_attr_e('Browse the site in', 'gioia'); ?>" role="button">
        <?php echo gioia_icon('globe'); ?>
      </span>
      <?php
      pll_the_languages([
        'dropdown'         => 1,
        'show_names'       => 1,
        'show_flags'       => 0,
        'force_home'       => 0,
        'hide_current'     => 1,
        'display_names_as' => 'slug',
        'hide_if_no_translation' => 0
      ]);
      ?>
    </div>
<?php
    $select = ob_get_clean();
    $select = str_replace('<select ', '<select class="pll-selector__select" ', $select);
    $select = str_replace('lang_choice_1', 'languages', $select);
    if (!$echo) return $select;
    echo $select;
  }
}
