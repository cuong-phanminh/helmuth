<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


$seadev_child_includes = array(
	'/setup.php',                           // Theme setup and custom theme supports.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/blocks.php',                          // Manage gutenberg block.
	'/shortcodes.php',                      // Custom shortcodes.
	'/functions.php'                      // Custom functions.
);

foreach ( $seadev_child_includes as $file ) {
	$filepath = locate_template( 'inc_child' . $file );
	if ( ! $filepath ) {
		trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
	}
	require_once $filepath;
}


function wpb_hook_javascript() {
	?>
		<script src="https://use.fontawesome.com/6d968b85e0.js"></script>
	<?php
}
add_action('wp_head', 'wpb_hook_javascript');