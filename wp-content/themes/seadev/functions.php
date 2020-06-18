<?php
/**
 * Seadev functions and definitions
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$seadev_includes = array(
	'/acf.php',                      				// Load ACF functions.
	'/advanced-theme-settings.php',         // Advanced theme settings.
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/widgets.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/hooks.php',                           // Custom hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/jetpack.php',                         // Load Jetpack compatibility file.
	'/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker.
	'/woocommerce.php',                     // Load WooCommerce functions.
	'/editor.php',                          // Load Editor functions.
	'/deprecated.php',                      // Load deprecated functions.
	'/blocks.php',                      		// Manage gutenberg block.
	'/shortcodes.php',                      // Custom shortcodes.
);

foreach ( $seadev_includes as $file ) {
	$filepath = locate_template( 'inc' . $file );
	if ( ! $filepath ) {
		trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
	}
	require_once $filepath;
}

//header widget 
function wpb_widgets_init() {
	register_sidebar( array(
	'name' => 'Header Widget',
	'id' => 'header-widget',
	'before_widget' => '<div class="hw-widget">',
	'after_widget' => '</div>',
	'before_title' => '<h2 class="hw-title">',
	'after_title' => '</h2>',
	) );
	
	}
	add_action( 'widgets_init', 'wpb_widgets_init' );
