<?php

/**
 * Gioia Starter Theme functions and definitions.
 *
 * @see     https://developer.wordpress.org/block-editor/how-to-guides/themes/create-block-theme/
 *
 * @package gioia
 * @since   1.0.0
 */

if (!defined('THEME_VERSION')) {
  $theme = wp_get_theme();
  $version = $theme->get('Version');
  if (wp_get_environment_type() !== 'production') {
    $version = current_time('U');
  }
  define('THEME_VERSION', $version);
}
if (!defined('THEME_WP_VERSION')) {
  $wp_version = get_bloginfo('version');
  define('THEME_WP_VERSION', $wp_version);
}
define('THEME_LOCALE', get_bloginfo('language'));
define('THEME_GOOGLE_FONTS', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700;800&family=Roboto:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap');

/**
 * Load theme utilities before theme setup.
 */
include_once get_template_directory() . '/inc/theme-utils.php';

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @link https://developer.wordpress.org/reference/functions/add_theme_support/
 */
if (!function_exists('gioia_theme_setup')) :
  function gioia_theme_setup() {
    // Add default posts and comments RSS feed links to <head>.
    add_theme_support('automatic-feed-links');

    // Enable support for post thumbnails and featured images.
    add_theme_support('post-thumbnails');

    // Let WordPress manage the document title to avoid hard-coded <title> tag.
    add_theme_support('title-tag');

    // Add support for page excerpt.
    add_post_type_support('page', 'excerpt');

    // Make this theme available for translation.
    load_theme_textdomain('gioia', get_template_directory() . '/languages');

    // Enable Block Editor styles on front-end.
    add_theme_support('wp-block-styles');
    // Enqueue Block Editor styles on back-end.
    add_theme_support('editor-styles');
    add_editor_style([
      THEME_GOOGLE_FONTS,
      'assets/css/editor.css'
    ]);
    // Unregister all default patterns.
    remove_theme_support('core-block-patterns');

    /**
     * Add support for core custom logo.
     *
     * @link https://codex.wordpress.org/Theme_Logo
     */
    add_theme_support('custom-logo', [
      'height'      => 200,
      'width'       => 200,
      'flex-width'  => true,
      'flex-height' => true
    ]);

    /**
     * Add and enable custom image sizes.
     *
     * @link https://developer.wordpress.org/reference/functions/add_image_size/
     */
    add_image_size('blog-thumbnail', 370, 330);

    // Remove some WordPress links included in <head>.
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

    // Remove default WordPress emoji.
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');

    // Remove default WordPress canonical and shortlink.
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'wp_shortlink_wp_head');
  }
endif;
add_action('after_setup_theme', 'gioia_theme_setup');

/**
 * Enqueue theme scripts and styles.
 *
 * @return void
 */
function gioia_scripts() {
  // Stylesheets
  wp_enqueue_style('google-fonts', THEME_GOOGLE_FONTS, [], null, 'all');
  wp_enqueue_style('bootstrap-reboot', get_template_directory_uri() . '/assets/css/reboot.min.css', [], '4.6.1', 'all');
  wp_enqueue_style('gioia-blocks', get_template_directory_uri() . '/assets/css/blocks.css', ['gioia-theme'], THEME_VERSION);
  wp_register_style('gioia-theme', get_template_directory_uri() . '/assets/css/main.css', [], THEME_VERSION, 'all');
  wp_add_inline_style('gioia-theme', ThemeUtils::create_theme_styles());
  wp_enqueue_style('gioia-theme');

  // Scripts
  wp_enqueue_script('jquery');
  wp_enqueue_script('gioia-blocks', get_template_directory_uri() . '/assets/js/blocks-theme.js', ['gioia-theme'], THEME_VERSION, true);
  wp_enqueue_script('gioia-theme', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], THEME_VERSION, true);
  wp_localize_script('gioia-theme', 'gioia_utils', array(
    'site_url'  => esc_url(site_url('/')),
    'ajax_url'  => esc_url(admin_url('/admin-ajax.php')),
    'language'  => esc_attr(explode('-', THEME_LOCALE)[0]),
    'loading'   => esc_attr__('Loading&hellip;', 'gioia'),
    'upload'    => esc_attr__('Choose file', 'gioia'),
    'select'    => esc_attr__('Choose option', 'gioia'),
    'search'    => esc_attr__('Search&hellip;', 'gioia'),
    'readmore'  => esc_attr__('Read more', 'gioia')
  ));
  wp_localize_script('gioia-theme', 'gioia_icons', ThemeUtils::create_block_icons());
  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}
add_action('wp_enqueue_scripts', 'gioia_scripts');

/**
 * Enqueue Block Editor assets.
 *
 * @link https://developer.wordpress.org/reference/hooks/enqueue_block_assets/
 */
function gioia_block_editor_assets() {
  // Stylesheets
  wp_enqueue_style('gioia-blocks', get_template_directory_uri() . '/assets/css/blocks.css', ['wp-edit-blocks'], THEME_VERSION);
  wp_add_inline_style('gioia-blocks', ThemeUtils::create_theme_styles());

  // Scripts
  wp_enqueue_script('gioia-editor', get_template_directory_uri() . '/assets/js/blocks-editor.js', ['wp-blocks', 'wp-dom-ready'], THEME_VERSION);
  wp_enqueue_script('gioia-blocks', get_template_directory_uri() . '/assets/js/blocks-theme.js', ['gioia-editor'], THEME_VERSION);
  wp_localize_script('gioia-editor', 'gioia_icons', ThemeUtils::create_block_icons());
}
add_action('enqueue_block_editor_assets', 'gioia_block_editor_assets', 20);

/**
 * If not set globally, force removal of the SVGs for the duotone styles.
 *
 * @link https://stackoverflow.com/questions/71290047/how-to-remove-front-svg-in-wordpress-5-9-version#71290180
 */
function gioia_remove_duotone_svg_styles() {
  $settings = wp_get_global_settings();
  if (is_array($settings) && isset($settings['color']['customDuotone']) && !$settings['color']['customDuotone']) {
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
  }
}
add_action('init', 'gioia_remove_duotone_svg_styles', 99);

/**
 * Add <link /> tag to preconnect Google Fonts before its handle.
 *
 * @param  string $html [default enqueued style output].
 * @param  string $handle [style's registered handle]
 * @return string $html [modified enqueued style output].
 */
function gioia_google_fonts_preconnect($html, $handle) {
  if ($handle === 'google-fonts') {
    $html = "<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n$html";
  }
  return $html;
}
add_filter('style_loader_tag', 'gioia_google_fonts_preconnect', 10, 2);

/**
 * Make sure to disable browser telephone format detection on iOS/Safari.
 *
 * @see    https://developer.apple.com/library/archive/documentation/AppleApplications/Reference/SafariHTMLRef/Articles/MetaTags.html#//apple_ref/doc/uid/TP40008193-SW1
 * @link   https://stackoverflow.com/questions/226131/how-to-disable-phone-number-linking-in-mobile-safari
 *
 * @return void
 */
function gioia_telephone_format_detection_meta() {
  echo "<meta name=\"format-detection\" content=\"telephone=no\" />\n";
}
add_action('wp_head', 'gioia_telephone_format_detection_meta', 11);

/**
 * Add theme color meta tag.
 *
 * @return void
 */
function gioia_theme_color_meta() {
  $default_color = ThemeUtils::get_color_palette()['primary']['color'];
  $theme_color = get_theme_mod('text_color', $default_color);
  echo "<meta name=\"theme-color\" content=\"" . esc_attr($theme_color) . "\" />\n";
}
add_action('wp_head', 'gioia_theme_color_meta', 20);

/**
 * Add theme favicon link to support some older browsers.
 *
 * @return void
 */
function gioia_favicon_link() {
  $default_icon = get_site_icon_url();
  $favicon_path = ABSPATH . '/favicon.ico';
  if (!empty($default_icon) && file_exists($favicon_path)) {
    echo "<link rel=\"shortcut icon\" href=\"" . site_url('/favicon.ico') . "\" />";
  }
}
add_action('wp_head', 'gioia_favicon_link', 100);

/**
 * Add a pingback URL auto-discovery header for single posts, pages, or attachments.
 *
 * @return void
 */
function gioia_pingback_header() {
  if (is_singular() && pings_open()) {
    printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
  }
}
add_action('wp_head', 'gioia_pingback_header');

/**
 * Move WordPress admin bar from top to bottom.
 *
 * @return void
 */
function gioia_admin_bar_styles() {
  if (is_admin_bar_showing()) {
    remove_action('wp_head', '_admin_bar_bump_cb');
    wp_add_inline_style('admin-bar', ThemeUtils::create_admin_styles());
  }
}
add_action('wp_print_styles', 'gioia_admin_bar_styles', 11);

/**
 * Add custom classes to the array of body classes.
 *
 * @param  array $classes [default body classes].
 * @return array $classes [modified body classes].
 */
function gioia_body_classes($classes) {
  $is_default_page = (bool) is_page() && get_page_template_slug() == '';
  if ($is_default_page || is_single() || is_archive() || is_search() || is_404()) {
    $classes[] = 'default-template';
  }
  return $classes;
}
add_filter('body_class', 'gioia_body_classes');

/**
 * Include a dedicated template for password protected posts.
 *
 * @param  string $template [default template name].
 * @return string $template [modified template name].
 */
function gioia_password_protected_template($template) {
  if (post_password_required()) {
    $pwd_template = locate_template('protected.php');
    $template = $pwd_template ?: $template;
  }
  return $template;
}
add_filter('template_include', 'gioia_password_protected_template', 10, 1);

/**
 * Handle WordPress excerpt:
 * - change the excerpt length
 * - change the excerpt more string
 */
function gioia_custom_excerpt_length($length) {
  return 30;
}
add_filter('excerpt_length', 'gioia_custom_excerpt_length', 99);
function gioia_custom_excerpt_more($more) {
  return '&hellip;';
}
add_filter('excerpt_more', 'gioia_custom_excerpt_more');

/**
 * Stop WordPress wrapping images in a <p> tag.
 */
function gioia_remove_ptags_on_image($content) {
  return preg_replace('/<p>(\s*)(<img .* \/>)(\s*)<\/p>/iU', '\2', $content);
}
add_filter('the_content', 'gioia_remove_ptags_on_image');

/**
 * Allow upload of non-standard file types  in the media library.
 *
 * @param  array $mime_types [default mime types].
 * @param  mixed $user [current logged-in user].
 * @return array $mime_types [modified mime types].
 */
function gioia_upload_mime_types($mime_types) {
  if (!isset($mime_types['webp'])) {
    $mime_types['webp'] = 'image/webp';
  }
  if (!isset($mime_types['svg']) && current_user_can('edit_posts')) {
    $mime_types['svg'] = 'image/svg+xml';
  }
  return $mime_types;
}
add_filter('upload_mimes', 'gioia_upload_mime_types', 110, 1);

/**
 * Handle WordPress Admin login screen and disable language selector.
 *
 * @see  https://make.wordpress.org/core/2021/12/20/introducing-new-language-switcher-on-the-login-screen-in-wp-5-9/
 *
 * @link https://codex.wordpress.org/Customizing_the_Login_Form
 */
function gioia_login_logo_link() {
  return home_url();
}
add_filter('login_headerurl', 'gioia_login_logo_link', 110, 0);
function gioia_login_logo_title() {
  return get_bloginfo('title');
}
add_filter('login_headertext', 'gioia_login_logo_title', 110, 0);
function gioia_login_form_style() {
  $site_logo_id = get_theme_mod('custom_logo', 0);
  wp_add_inline_style('login', ThemeUtils::create_login_styles($site_logo_id));
}
add_action('login_enqueue_scripts', 'gioia_login_form_style', 110);
if (version_compare(THEME_WP_VERSION, '5.9', '>=')) {
  add_filter('login_display_language_dropdown', '__return_false');
}

/**
 * Disable XML-RPC by default to avoid brute force attacks.
 *
 * @return void
 */
add_filter('xmlrpc_enabled', '__return_false', 99);
remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
remove_action('template_redirect', 'rest_output_link_header', 11);

/**
 * Disable 'users' route from REST API for security reason.
 *
 * @method rest_authentication_errors
 *
 * @param  mixed $access [authentication response].
 * @return mixed [authentication error or default].
 */
function gioia_rest_authentication_handler($access) {
  // define user permission.
  $has_permission = false;
  if ($user_id = get_current_user_id()) {
    $user = get_user_by('id', $user_id);
    $allowed_roles = array('administrator', 'editor', 'author');
    $has_permission = (is_array($user->roles) && !empty(array_intersect($allowed_roles, $user->roles)));
  }

  // get the current rest route.
  $rest_route = $GLOBALS['wp']->query_vars['rest_route'];
  if (!empty($rest_route) || $rest_route != '/') {
    $rest_route = untrailingslashit($rest_route);
  }

  // check for invalid access.
  if (strpos($rest_route, 'users') !== false && !$has_permission) {
    return new WP_Error(
      'rest_cannot_access',
      esc_html('You cannot access this REST API route.'),
      ['status' => rest_authorization_required_code()]
    );
  }

  return $access;
}
add_filter('rest_authentication_errors', 'gioia_rest_authentication_handler', 99, 1);

/**
 * Theme-based icon renderer.
 *
 * @see ThemeUtils::create_svg_icon
 */
function gioia_icon($name) {
  return ThemeUtils::create_svg_icon($name);
}

/**
 * Theme-based placeholder thumbnail.
 *
 * @return void
 */
function gioia_placeholder_thumbail() {
  $placeholder_src = get_template_directory_uri() . '/assets/img/post-placeholder.jpg';
  echo '<img src="' . esc_url($placeholder_src) . '" width="370" height="330" alt="' . esc_attr__('Placeholder image', 'gioia') . '" />';
}

/**
 * Helper to get video preview thumbnail by source URL.
 *
 * @see ThemeUtils::create_video_preview
 */
function gioia_video_thumbnail($video_url, $image_id) {
  return ThemeUtils::create_video_preview($video_url, $image_id);
}

/**
 * Helper to get theme social networks.
 *
 * @see ThemeUtils::create_social_networks
 */
function gioia_social_networks() {
  return ThemeUtils::create_social_networks();
}

/**
 * Helper to get a countries' list to use as dataset.
 *
 * @return array $countries
 */
function gioia_countries() {
  $countries = array();
  $file_path = get_template_directory() . '/inc/data/countries.json';
  if (file_exists($file_path)) {
    $file_data = file_get_contents($file_path);
    foreach (json_decode($file_data, true) as $item) {
      $countries[$item['code']] = $item['country'];
    }
  }
  return $countries;
}

/**
 * Load customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load custom post types and taxonomies.
 */
require get_template_directory() . '/inc/custom-types.php';
require get_template_directory() . '/inc/custom-taxonomies.php';

/**
 * Load Yoast SEO integrations.
 */
if (class_exists('WPSEO_Options')) {
  require get_template_directory() . '/inc/custom-seo.php';
}

/**
 * Load Polylang integrations.
 */
if (class_exists('Polylang')) {
  require get_template_directory() . '/inc/custom-polylang.php';
}

/**
 * Load Contact Form 7 integrations.
 */
if (class_exists('WPCF7')) {
  require get_template_directory() . '/inc/custom-cf7.php';
}

/**
 * Load ACF & Blocks integrations.
 */
if (class_exists('ACF')) {
  require get_template_directory() . '/inc/custom-acf.php';
}
require get_template_directory() . '/inc/theme-blocks.php';
