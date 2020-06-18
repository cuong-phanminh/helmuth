<?php
/**
 * Seadev modify editor
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers an editor stylesheet for the theme.
 */

function seadev_add_editor_style() {
  wp_enqueue_style('seadev-editor-styles', get_template_directory_uri().'/css/editor.css');
}
add_action('admin_enqueue_scripts', 'seadev_add_editor_style');
