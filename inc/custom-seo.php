<?php
if ( !defined('ABSPATH') ) {
  exit;
}

/**
 * Theme-based breadcrumb with Yoast.
 *
 * @method gioia_theme_breadcrumb
 */
if ( !function_exists('gioia_theme_breadcrumb') ) {
  function gioia_theme_breadcrumb() {
    yoast_breadcrumb('<ol class="breadcrumb"><li class="breadcrumb__item">', '</li></ol>');
  }
  function gioia_yoast_breadcrumb_items() {
    $yoast_options = get_option('wpseo_titles', []);
    $separator = isset($yoast_options['breadcrumbs-sep']) ? $yoast_options['breadcrumbs-sep'] : '&#155;';
    return "</li>&nbsp;$separator<li class=\"breadcrumb__item\">";
  }
  add_filter('wpseo_breadcrumb_separator', 'gioia_yoast_breadcrumb_items', 10);
  function gioia_yoast_breadcrumb_separator() {
    return '</li><li>';
  }
  add_filter('wpseo_breadcrumb_separator', 'gioia_yoast_breadcrumb_separator', 10);
}

/**
 * Change the metabox priority to show it at the bottom of the page.
 *
 * @return string
 */
function gioia_yoast_metabox_priority() {
  return 'low';
}
add_filter('wpseo_metabox_prio', 'gioia_yoast_metabox_priority');
