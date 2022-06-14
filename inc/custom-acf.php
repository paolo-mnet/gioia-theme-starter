<?php
if ( !defined('ABSPATH') ) {
  exit;
}

/**
 * ACF Theme integrations.
 *
 * @link    https://www.advancedcustomfields.com/
 *
 * @package gioia
 */
function gioia_acf_admin_head() {
?>
  <style type="text/css">
    .acf-field input[readonly][disabled],
    .acf-field textarea[readonly][disabled] {background: #fbfbfb; cursor: text}
    #acf-term-fields>.acf-field>.acf-label label {font-size: 13px !important}
    .form-table>tbody>.acf-field>.acf-label label {font-weight: 600 !important}
    .acf-accordion .acf-accordion-title label {font-size: 13px !important; font-weight: 500 !important}
    .acf-accordion .acf-accordion-title svg.acf-accordion-icon {right: 16px !important}
    .acf-field.editor-max-height > .acf-input .mce-edit-area > iframe,
    .acf-field.editor-max-height > .acf-input .wp-editor-container textarea {height: 220px !important}
    .acf-field-gallery.gallery-max-height > .acf-input > .acf-gallery {height: 266px !important}
    .acf-field.field-message pre {display: inline; padding: 1px 2px; background-color: #f4f4f4}
    .acf-field.columns-layout .acf-flexible-content .values {display: flex; flex-wrap: wrap}
    .acf-field.columns-layout .acf-flexible-content .values > .layout {flex: 0 0 33.333%; max-width: calc(33.333% - 2px); margin: 0 !important}
    .acf-bgimage .acf-image-uploader.has-value .image-wrap {max-width: inherit !important; width: 100% !important;}
    .acf-bgimage .acf-image-uploader.has-value .image-wrap img {max-height: 480px !important; width: 100% !important; object-fit: cover}
    .acf-block-component.acf-block-body .acf-field.acf-field-accordion,
    .block-editor-block-inspector .acf-block-panel .acf-block-fields > .acf-field:not(.acf-field-accordion):not(.block-editor-visible) {display: none !important}
    .block-editor-block-inspector .acf-block-panel .acf-block-fields {border-top-width: 0px}
    .acf-date-picker input.hasDatepicker {cursor: pointer;}
    .acf-range-wrap input[type=number] {min-width: 56px}
  </style>
<?php
}
add_action('acf/input/admin_head', 'gioia_acf_admin_head');

function gioia_acf_admin_footer() {
  $theme_palettes = wp_list_pluck( ThemeUtils::get_color_scheme(), 'color' );
?>
  <script type="text/javascript">
    (function($) {
      acf.add_filter('color_picker_args', function(args, $field) {
        args.palettes = <?= wp_json_encode( array_values($theme_palettes) ); ?>;
        return args;
      });
    })(jQuery);
  </script>
<?php
}
add_action('acf/input/admin_footer', 'gioia_acf_admin_footer');

function gioia_acf_google_map_key() {
  // TODO: how to handle options
  $google_api_key = get_theme_mod('gmap_api_key', '');
  acf_update_setting('google_api_key', $google_api_key);
}
add_filter('acf/init', 'gioia_acf_google_map_key');

function gioia_acf_relationship_query($args, $field, $post_id) {
  if ( strpos($field['name'], '_related') !== false ) {
    $args['post__not_in'] = [$post_id];
  }
  return $args;
}
add_filter('acf/fields/relationship/query', 'gioia_acf_relationship_query', 10, 3);

function gioia_acf_dynamic_icons_select($field) {
  $field['choices'] = array();
  if ( $icon_names = ThemeUtils::get_icons() ) {
    foreach($icon_names as $name => $label) {
      $field['choices'][esc_attr($name)] = esc_html($label);
    }
  }
  return $field;
}
add_filter('acf/load_field/name=link_icon', 'gioia_acf_dynamic_icons_select', 10, 1);
