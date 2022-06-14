<?php
if (!defined('ABSPATH')) {
  exit;
}

acf_register_block_type([
  'name'              => 'accordion',
  'category'          => 'gioia',
  'title'             => _x('Accordion', 'Block Editor', 'gioia'),
  'description'       => _x('Show an accordion, useful for FAQ and support.', 'Block Editor', 'gioia'),
  'icon'              => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4v8h16V4H0zm15 7H1V7h14v4zM0 0h16v3H0V0zM0 13h16v3H0v-3z"/></svg>',
  'mode'              => 'preview',
  'align'             => '',
  'keywords'          => ['accordion', 'panels'],
  'supports'          => array(
    'anchor'        => true,
    'mode'          => false,
    'align'         => ['wide', 'full'],
    'align_text'    => false,
    'align_content' => false,
    'color'         => false,
    'jsx'           => true,
    'multiple'      => true
  ),
  'render_template'   => get_template_directory() . '/inc/blocks/accordion/template.php'
]);
