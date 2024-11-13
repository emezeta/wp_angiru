<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Enable WP_DEBUG mode
// define( 'WP_DEBUG', true );

// Enable Debug logging to the /wp-content/debug.log file
// define( 'WP_DEBUG_LOG', true );


// Incluir y registrar el widget
function include_visitas_obra() {
    $path = get_stylesheet_directory(). '/visitas-obra/visitas-obra.php';
    if (file_exists($path)) {
        require_once $path;
    }
}
add_action('after_setup_theme', 'include_visitas_obra');

// Registrar el widget
function registrar_visitas_obras() {
    register_widget('VisitasObraWidget');
}
add_action('widgets_init', 'registrar_visitas_obras');



// Registrar block
function block_angiru_scripts() {
  wp_enqueue_script(
    'block-angiru-script',
    plugins_url('visitas-obra/index.js', __FILE__),
    array('wp-blocks', 'wp-element'),
    '1.0.0',
    true
  );
  wp_enqueue_style(
    'block-angiru-style',
    plugins_url('visitas-obra/style.css', __FILE__),
    array(),
    '1.0.0'
  );
}
add_action('enqueue_block_editor_assets', 'block_angiru_scripts');

global $pagenow;

if ( $pagenow === 'widgets.php' ) {
    return array( 'wp-edit-widgets',
        'wp-blocks',
        'wp-i18n',
        'wp-element', );
}

return array( 'wp-editor',
        'wp-blocks',
        'wp-i18n',
        'wp-element', );


