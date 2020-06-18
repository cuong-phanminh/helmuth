<?php
/**
 * Check and setup theme's default settings
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'seadev_setup_theme_default_settings' ) ) {
	function seadev_setup_theme_default_settings() {

		// check if settings are set, if not set defaults.
		// Caution: DO NOT check existence using === always check with == .
		// Latest blog posts style.
		$seadev_posts_index_style = get_theme_mod( 'seadev_posts_index_style' );
		if ( '' == $seadev_posts_index_style ) {
			set_theme_mod( 'seadev_posts_index_style', 'default' );
		}

		// Sidebar position.
		$seadev_sidebar_position = get_theme_mod( 'seadev_sidebar_position' );
		if ( '' == $seadev_sidebar_position ) {
			set_theme_mod( 'seadev_sidebar_position', 'right' );
		}

		// Container width.
		$seadev_container_type = get_theme_mod( 'seadev_container_type' );
		if ( '' == $seadev_container_type ) {
			set_theme_mod( 'seadev_container_type', 'container' );
		}
	}
}
