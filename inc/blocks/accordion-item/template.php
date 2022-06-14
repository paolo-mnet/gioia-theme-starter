<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Block: Accordion Item
 *
 * @param   array  $block
 * @param   string $content
 * @param   bool   $is_preview
 * @param   int    $post_id
 */

// Get ACF block values.
$panel_id = get_field('panel_id') ?: "panel-$block[id]";
$panel_title = get_field('panel_title') ?: false;
$panel_expanded = get_field('panel_expanded') ?: false;
if ( $panel_expanded && $is_preview ) {
  $panel_expanded = false;
}

// Set allowed inner blocks.
$allowed_blocks = wp_json_encode([
  'acf/simple-cta',
  'acf/datasheet',
  'core/paragraph',
  'core/heading',
  'core/columns',
  'core/list'
]);

// Prepare template to render.
$classes = !empty($block['className']) ? $block['className'] : '';
get_template_part('inc/blocks/accordion-item/abstract', null, [
  'id'       => $panel_id,
  'title'    => $panel_title,
  'expanded' => $panel_expanded,
  'blocks'   => $allowed_blocks,
  'preview'  => $is_preview,
  'classes'  => $classes
]);
