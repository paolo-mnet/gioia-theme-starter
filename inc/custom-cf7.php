<?php
if ( !defined('ABSPATH') ) {
  exit;
}

/**
 * Stop wrap form control into <p> tag.
 *
 * @var void
 */
add_filter('wpcf7_autop_or_not', '__return_false');

/**
 * Handle CF7 form elements to add theme-based UI.
 *
 * @param  string $form_elements [default output].
 * @return string $form_elements [modified output].
 */
function gioia_cf7_form_elements($form_elements) {
  if ( strpos($form_elements, 'wpcf7-submit') !== false ) {
    $form_elements = str_replace(
      'wpcf7-form-control has-spinner wpcf7-submit',
      'wpcf7-submit has-spinner btn btn-primary',
      $form_elements
    );
  }
  if ( strpos($form_elements, '---') !== false ) {
    $i18n_label = esc_attr__('Choose option', 'gioia');
    $form_elements = str_replace(
      '<option value="">---</option>',
      '<option value="" selected disabled>'. $i18n_label .'</option>',
      $form_elements
    );
  }
  return $form_elements;
}
add_filter('wpcf7_form_elements', 'gioia_cf7_form_elements', 10, 1);

/**
 * Create CF7 dynamic dataset for some fields (country, post type etc).
 *
 * @return void
 */
function gioia_cf7_dynamic_data($n, $options, $args) {
  if ( in_array('countries', $options) && function_exists('gioia_countries') ) {
    $countries = gioia_countries();
    return $countries;
  }
  return null;
}
add_filter('wpcf7_form_tag_data_option', 'gioia_cf7_dynamic_data', 10, 3);

/**
 * Handle CF7 invalid state(s) and redirect on submit.
 *
 * @return void
 */
function gioia_cf7_submit_handler() {
  // NOTE: get the correct page ID and replace 4441
  $thank_you_page_id = function_exists('pll_get_post') ? pll_get_post(4441) : 4441;
?>
  <script type="text/javascript">
    (function($) {
      var wpcf7submit;
      $(document).on('submit', 'form.wpcf7-form', function(e) {
        if ( $(e.target).find('.wpcf7-form-group.required').length ) {
          $(e.target).find('.wpcf7-form-group').removeClass('required');
        }
      });
      $(document).on('wpcf7submit', function(e) {
        if ( e.detail.status === 'validation_failed' ) {
          clearTimeout(wpcf7submit);
          wpcf7submit = setTimeout(function() {
            $(e.target).find('[aria-invalid="true"]').parents('.wpcf7-form-group').addClass('required');
          }, 10);
        }
      });
      $(document).on('wpcf7mailsent', function() {
        var thankYouPageUrl = '<?= esc_url( get_permalink($thank_you_page_id) ); ?>';
        if (!thankYouPageUrl) return false;
        return window.location.href = thankYouPageUrl;
      });
    })(jQuery);
  </script>
<?php
}
add_action('wp_footer', 'gioia_cf7_submit_handler', 11);
