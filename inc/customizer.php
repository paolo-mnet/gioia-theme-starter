<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Gioia Starter Theme Customizer.
 *
 * @param   WP_Customize_Manager $wp_customize Theme Customizer object.
 * @package gioia
 */
function gioia_customize_register($wp_customize) {
  // Theme sanitizer(s).
  function gioia_sanitize_checkbox($input) {
    return ((isset($input) && $input == true) ? true : false);
  }

  // Theme additional settings.
  $wp_customize->add_section('additional_settings', array(
    'title'       => __('Additional settings', 'gioia'),
    'description' => esc_html__('Set some additional settings for the site here.', 'gioia'),
    'priority'    => 85
  ));
  $wp_customize->add_setting('enable_search', array(
    'default'   => '',
    'sanitize_callback' => 'gioia_sanitize_checkbox'
  ));
  $wp_customize->add_control('enable_search', array(
    'type'    => 'checkbox',
    'section' => 'additional_settings',
    'label'   => __('Enable search', 'gioia'),
    'description' => __('Choose whether or not to show the search button in the main menu.', 'gioia')
  ));
  if (class_exists('WooCommerce')) {
    if (function_exists('gioia_woocommerce_cart_button')) {
      $wp_customize->add_setting('enable_cart', array(
        'default'   => '',
        'sanitize_callback' => 'gioia_sanitize_checkbox'
      ));
      $wp_customize->add_control('enable_cart', array(
        'type'    => 'checkbox',
        'section' => 'additional_settings',
        'label'   => __('Enable cart', 'gioia'),
        'description' => __('Choose whether or not to show the shopping cart button in the main menu.', 'gioia')
      ));
    }
    if (function_exists('gioia_woocommerce_loop_columns')) {
      $wp_customize->add_setting('shop_loop_columns', array(
        'default'   => '',
        'sanitize_callback' => 'absint'
      ));
      $wp_customize->add_control('shop_loop_columns', array(
        'type'    => 'number',
        'section' => 'additional_settings',
        'label'   => __('Products per row', 'gioia'),
        'description' => __('Choose how many products to show per row, on the shop/listing pages.', 'gioia'),
        'input_attrs' => array(
          'min'  => 1,
          'max'  => 6,
          'step' => 1
        )
      ));
    }
  }
  $wp_customize->add_setting('gtag_code', array(
    'default'   => '',
    'transport' => 'refresh',
    'sanitize_callback' => 'wp_filter_nohtml_kses'
  ));
  $wp_customize->add_control('gtag_code', array(
    'type'    => 'text',
    'section' => 'additional_settings',
    'label'   => __('UA Code', 'gioia'),
    'description' => __('The UA code (or tracking ID) is a string that looks like this: UA-000000-2.', 'gioia'),
    'input_attrs' => array(
      'maxlength' => 20
    )
  ));
  $wp_customize->add_setting('gmap_api_key', array(
    'default'   => '',
    'transport' => 'refresh',
    'sanitize_callback' => 'wp_filter_nohtml_kses'
  ));
  $wp_customize->add_control('gmap_api_key', array(
    'type'    => 'text',
    'section' => 'additional_settings',
    'label'   => __('Google Maps API Key', 'gioia'),
    'description' => __('The API Key is a key used to authenticate the requests associated with this site and useful for the maps to work correctly.', 'gioia'),
    'input_attrs' => array(
      'maxlength' => 50
    )
  ));

  // Theme social networks.
  $social_networks = ThemeUtils::get_social();
  $wp_customize->add_section('social_networks', array(
    'title'       => __('Social networks', 'gioia'),
    'description' => esc_html__('Set up your site\'s social networks.', 'gioia'),
    'priority'    => 95
  ));
  foreach ($social_networks as $name => $label) {
    $wp_customize->add_setting($name, array(
      'default'   => '',
      'transport' => 'refresh',
      'sanitize_callback' => 'esc_url_raw'
    ));
    $wp_customize->add_control($name, array(
      'type'    => 'url',
      'section' => 'social_networks',
      'label'   => $label,
    ));
  }
}
add_action('customize_register', 'gioia_customize_register');
