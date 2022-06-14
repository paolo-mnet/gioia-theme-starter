<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Block: Accordion
 *
 * @param   array  $block
 * @param   string $content
 * @param   bool   $is_preview
 * @param   int    $post_id
 */
$anchor_id = "accordion-$block[id]";
if ( !empty($block['anchor']) ) {
  $anchor_id = $block['anchor'];
}

// Set block classes.
$classes = 'accordion';
if ( !empty($block['className']) ) {
  $classes .= sprintf(' %s', $block['className']);
}
if ( !empty($block['align']) ) {
  $classes .= sprintf(' align%s', $block['align']);
}
if ($is_preview) $classes .= ' is-preview';

// Set allowed inner blocks.
$allowed_blocks = wp_json_encode([
  'acf/accordion-item'
]);

// Get ACF block values.
$accordion_multiple = get_field('accordion_multiple') ?: false;
$accordion_desc = get_field('accordion_desc') ?: "Accordion {$anchor_id}";
?>

<div id="<?= esc_attr($anchor_id); ?>"
     class="<?= esc_attr($classes); ?>"
     aria-label="<?= esc_attr($accordion_desc); ?>"
     data-multiple="<?= esc_attr($accordion_multiple ? 'true' : 'false'); ?>">

  <?php if($block['align'] === 'full'): ?><div class="accordion__container"><?php endif; ?>

  <InnerBlocks allowedBlocks="<?= esc_attr($allowed_blocks); ?>" />

  <?php if($block['align'] === 'full'): ?></div><?php endif; ?>

</div>
