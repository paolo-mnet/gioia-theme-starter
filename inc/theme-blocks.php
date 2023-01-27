<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Register Theme custom blocks.
 *
 * @link  https://www.advancedcustomfields.com/resources/acf_register_block_type/
 * @since ACF 5.8
 */
if (function_exists('acf_register_block_type')) {

  function gioia_register_theme_blocks_category($default_categories) {
    $theme_blocks_category = array([
      'icon'  => null,
      'slug'  => 'gioia',
      'title' => _x('Gioia Starter Theme', 'Block Editor', 'gioia')
    ]);
    return array_merge(
      $theme_blocks_category,
      $default_categories
    );
  }
  add_filter('block_categories_all', 'gioia_register_theme_blocks_category', 10);

  function gioia_register_theme_blocks() {
    $theme_blocks = array(
      'accordion',
      'accordion-item'
    );
    foreach ($theme_blocks as $block_name) {
      require_once get_template_directory() . "/inc/blocks/{$block_name}/register.php";
    }
  }
  add_action('acf/init', 'gioia_register_theme_blocks');
}

/**
 * Handle custom styles for block(s).
 *
 * @link   https://developer.wordpress.org/reference/functions/register_block_style/
 * @return void
 */
function gioia_register_block_styles() {
  register_block_style('core/list', [
    'is_default'   => false,
    'name'         => 'two-columns',
    'label'        => _x('Two columns', 'Block Editor', 'gioia')
  ]);
}
add_action('init', 'gioia_register_block_styles', 11);

/**
 * Register theme-based block pattern(s).
 *
 * @link   https://developer.wordpress.org/reference/functions/register_block_pattern/
 * @return void
 */
function gioia_register_block_patterns() {
  register_block_pattern(
    'gioia/example',
    array(
      'title'         => _x('My First Block Pattern', 'Block Editor', 'gioia'),
      'description'   => _x('This is my first block pattern', 'Block Editor', 'gioia'),
      'content'       => '<!-- wp:paragraph --><p>A single paragraph block style</p><!-- /wp:paragraph -->',
      'keywords'      => ['demo', 'example'],
      'categories'    => ['text'],
      'viewportWidth' => 768
    )
  );
}
add_action('init', 'gioia_register_block_patterns', 11);

/**
 * Render Photoswipe (HTML dialog + assets) only if exists its block.
 *
 * @return void
 */
function gioia_block_photoswipe_dialog() {
  if (has_block('acf/gallery') || has_block('core/gallery')) {
    get_template_part('template-parts/partials/photoswipe');
  }
}
add_action('wp_footer', 'gioia_block_photoswipe_dialog', 9);
function gioia_block_photoswipe_assets() {
  if (has_block('acf/gallery') || has_block('core/gallery')) {
    wp_enqueue_style('photoswipe', get_template_directory_uri() . '/assets/css/photoswipe.min.css', [], '4.1.3', 'screen');
    wp_enqueue_style('photoswipe-ui', get_template_directory_uri() . '/assets/css/photoswipe-ui.css', [], '4.1.3', 'screen');
    wp_enqueue_script('photoswipe', get_template_directory_uri() . '/assets/js/photoswipe.min.js', [], '4.1.3', true);
    wp_enqueue_script('photoswipe-ui', get_template_directory_uri() . '/assets/js/photoswipe-ui.min.js', [], '4.1.3', true);
  }
}
add_action('wp_enqueue_scripts', 'gioia_block_photoswipe_assets', 9);

/**
 * Filter the block(s) output.
 *
 * @param  string $output
 * @param  array  $block
 * @return string $output
 */
function gioia_render_block($output, $block) {
  switch ($block['blockName']) {
    case 'core/button':
      if (isset($block['attrs']['icon']) && ($icon = $block['attrs']['icon'])) {
        $icon_svg = gioia_icon($icon);
        $icon_pos = $block['attrs']['icon_pos'] ?? 'left';
        $output = preg_replace('/<a (.*?)>(.*?)<\/a>/', '<a $1>' . ($icon_pos == 'right' ? '<span>$2</span>' . $icon_svg : $icon_svg . '<span>$2</span>') . '</a>', $output);
      }
      break;
    case 'core/gallery':
      $output = str_replace('<ul class="blocks-gallery-grid">', '<ul class="blocks-gallery-grid" itemscope itemtype="http://schema.org/ImageGallery" data-gallery>', $output);
      $output = str_replace('<figure>', '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" role="listitem">', $output);
      preg_match_all('/<img(?=.*?data-full-url="(.*?)")[^>]*?>/i', $output, $matches);
      foreach ($matches[0] as $i => $image) {
        $image_url = $matches[1][$i];
        $image_id = attachment_url_to_postid($image_url);
        $image_meta = wp_get_attachment_metadata($image_id);
        $image_full_size = array($image_meta['width'], $image_meta['height']);
        $output = str_replace($image, "<a href=\"{$image_url}\" itemprop=\"contentUrl\" data-size=\"" . implode('x', $image_full_size) . "\">{$image}</a>", $output);
      }
      $output = str_replace('<img', '<img itemprop="thumbnail"', $output);
      break;
  }
  return $output;
}
add_filter('render_block', 'gioia_render_block', 110, 2);
