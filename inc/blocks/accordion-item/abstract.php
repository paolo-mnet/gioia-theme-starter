<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
if (!isset($args)) {
  exit;
}

// Get component options.
$panel_id = isset($args['id']) ? $args['id'] : uniqid();
$panel_title = isset($args['title']) ? $args['title'] : false;
$preview = isset($args['preview']) ? $args['preview'] : false;
$panel_expanded = isset($args['expanded']) ? $args['expanded'] : false;

// Create component classes.
$classes = 'accordion__panel';
if ($panel_expanded) {
  $classes .= ' accordion__panel--in';
}
if (isset($args['classes'])) {
  $classes .= " {$args['classes']}";
}
$icon_name = !$panel_expanded ? 'plus' : 'minus';
?>

<div class="<?= esc_attr($classes); ?>">

  <?php if (!empty($panel_title)) : ?>
    <h6 class="accordion__panel__summary" id="<?= esc_attr("heading-{$panel_id}"); ?>">
      <button type="button" aria-controls="<?= esc_attr($panel_id); ?>" aria-expanded="<?= esc_attr($panel_expanded ? 'true' : 'false'); ?>">
        <?= esc_html($panel_title); ?>
        <?= gioia_icon($icon_name); ?>
      </button>
    </h6>

    <div class="accordion__panel__details" id="<?= esc_attr($panel_id); ?>" aria-hidden="<?= esc_attr(!$panel_expanded ? 'true' : 'false'); ?>">
      <div aria-labelledby="<?= esc_attr("heading-{$panel_id}"); ?>">
        <?php if (isset($args['blocks']) && ($allowed_blocks = $args['blocks'])) : ?>
          <InnerBlocks allowedBlocks="<?= esc_attr($allowed_blocks); ?>" />
        <?php endif; ?>
        <?php if (isset($args['content']) && ($content = $args['content'])) {
          echo wp_kses_post($content);
        } ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($preview && !$panel_title) : ?>
    <p class="acf-block-preview__notice"><?php _ex('Set the accordion panel title.', 'Block Editor', 'gioia'); ?></p>
  <?php endif; ?>

</div>
