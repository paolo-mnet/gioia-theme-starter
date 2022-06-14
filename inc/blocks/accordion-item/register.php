<?php
if (!defined('ABSPATH')) {
  exit;
}

acf_register_block_type([
  'name'              => 'accordion-item',
  'category'          => 'gioia',
  'title'             => _x('Accordion Item', 'Block Editor', 'gioia'),
  'description'       => _x('Handle the single element inside the accordion.', 'Block Editor', 'gioia'),
  'icon'              => 'plus-alt2',
  'mode'              => 'preview',
  'align'             => '',
  'keywords'          => ['accordion item', 'panel'],
  'parent'            => ['acf/accordion'],
  'supports'          => array(
    'anchor'        => false,
    'mode'          => false,
    'align'         => false,
    'align_text'    => true,
    'align_content' => false,
    'color'         => [
      'color'       => true,
      'background'  => false
    ],
    'jsx'           => true,
    'multiple'      => true
  ),
  'render_template'   => get_template_directory() . '/inc/blocks/accordion-item/template.php'
]);
