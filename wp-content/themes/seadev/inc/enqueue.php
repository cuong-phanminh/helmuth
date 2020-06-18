<?php
/**
 * Seadev enqueue scripts
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'seadev_scripts' ) ) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function seadev_scripts() {
		// Get the theme data.
		$the_theme     = wp_get_theme();
		$theme_version = $the_theme->get( 'Version' );

		$css_version = $theme_version . '.' . filemtime( get_template_directory() . '/css/theme.min.css' );

		wp_enqueue_style( 'slick-styles', get_template_directory_uri() . '/assets/slick/slick.css', array(), null );
		wp_enqueue_style( 'slick-theme-styles', get_template_directory_uri() . '/assets/slick/slick-theme.css', array(), null );

		wp_enqueue_style( 'seadev-styles', get_template_directory_uri() . '/css/theme.min.css', array(), $css_version );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-migrate' );

		wp_enqueue_script( 'slick-scripts', get_template_directory_uri() . '/assets/slick/slick.min.js', array(), null, true );

		$js_version = $theme_version . '.' . filemtime( get_template_directory() . '/js/theme.min.js' );
		wp_enqueue_script( 'seadev-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $js_version, true );
		wp_enqueue_script( 'seadev-custom-scripts', get_template_directory_uri() . '/js/scripts.js', array(), $js_version, true );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
} // endif function_exists( 'seadev_scripts' ).

add_action( 'wp_enqueue_scripts', 'seadev_scripts' );

if ( ! function_exists( 'seadev_block_admin_scripts' ) ) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function seadev_block_admin_scripts() {

		wp_enqueue_style( 'slick-styles', get_template_directory_uri() . '/assets/slick/slick.css', array(), null );
		wp_enqueue_style( 'slick-theme-styles', get_template_directory_uri() . '/assets/slick/slick-theme.css', array(), null );
		wp_enqueue_script( 'slick-scripts', get_template_directory_uri() . '/assets/slick/slick.min.js', array(), null, true );
	}
}

add_action( 'enqueue_block_editor_assets', 'seadev_block_admin_scripts' );
