<?php
/**
 * Seadev Theme Customizer
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
if ( ! function_exists( 'seadev_customize_register' ) ) {
	/**
	 * Register basic customizer support.
	 *
	 * @param object $wp_customize Customizer reference.
	 */
	function seadev_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	}
}
add_action( 'customize_register', 'seadev_customize_register' );

if ( ! function_exists( 'seadev_theme_customize_register' ) ) {
	/**
	 * Register individual settings through customizer's API.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer reference.
	 */
	function seadev_theme_customize_register( $wp_customize ) {

		// Theme layout settings.
		$wp_customize->add_section(
			'seadev_theme_layout_options',
			array(
				'title'       => __( 'Theme Layout Settings', 'seadev' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Container width and sidebar defaults', 'seadev' ),
				'priority'    => 160,
			)
		);

		// Header layout settings.
		$wp_customize->add_section(
			'seadev_header_layout_options',
			array(
				'title'       => __( 'Header Layout Settings', 'seadev' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Setting for layout of header', 'seadev' ),
				'priority'    => 170,
			)
		);

		/**
		 * Select sanitization function
		 *
		 * @param string               $input   Slug to sanitize.
		 * @param WP_Customize_Setting $setting Setting instance.
		 * @return string Sanitized slug if it is a valid choice; otherwise, the setting default.
		 */
		function seadev_theme_slug_sanitize_select( $input, $setting ) {

			// Ensure input is a slug (lowercase alphanumeric characters, dashes and underscores are allowed only).
			$input = sanitize_key( $input );

			// Get the list of possible select options.
			$choices = $setting->manager->get_control( $setting->id )->choices;

				// If the input is a valid key, return it; otherwise, return the default.
				return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

		}

		$wp_customize->add_setting(
			'seadev_container_type',
			array(
				'default'           => 'container',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'seadev_theme_slug_sanitize_select',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'seadev_container_type',
				array(
					'label'       => __( 'Container Width', 'seadev' ),
					'description' => __( 'Choose between Bootstrap\'s container and container-fluid', 'seadev' ),
					'section'     => 'seadev_theme_layout_options',
					'settings'    => 'seadev_container_type',
					'type'        => 'select',
					'choices'     => array(
						'container'       => __( 'Fixed width container', 'seadev' ),
						'container-fluid' => __( 'Full width container', 'seadev' ),
					),
					'priority'    => '10',
				)
			)
		);

		$wp_customize->add_setting(
			'seadev_sidebar_position',
			array(
				'default'           => 'right',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'seadev_sidebar_position',
				array(
					'label'             => __( 'Sidebar Positioning', 'seadev' ),
					'description'       => __(
						'Set sidebar\'s default position. Can either be: right, left, both or none. Note: this can be overridden on individual pages.',
						'seadev'
					),
					'section'           => 'seadev_theme_layout_options',
					'settings'          => 'seadev_sidebar_position',
					'type'              => 'select',
					'sanitize_callback' => 'seadev_theme_slug_sanitize_select',
					'choices'           => array(
						'right' => __( 'Right sidebar', 'seadev' ),
						'left'  => __( 'Left sidebar', 'seadev' ),
						'both'  => __( 'Left & Right sidebars', 'seadev' ),
						'none'  => __( 'No sidebar', 'seadev' ),
					),
					'priority'          => '20',
				)
			)
		);

		$wp_customize->add_setting(
			'seadev_header_toggle',
			array(
				'default'           => 'navbar-expand-md',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'seadev_theme_slug_sanitize_select',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'seadev_header_toggle',
				array(
					'label'       => __( 'Toggle Breakpoint', 'seadev' ),
					'description' => __( 'Choose breakpoint to show header responsive mode', 'seadev' ),
					'section'     => 'seadev_header_layout_options',
					'settings'    => 'seadev_header_toggle',
					'type'        => 'select',
					'choices'     => array(
						'navbar-collapse'       => __( 'Always collapse', 'seadev' ),
						'navbar-expand-xl'       => __( 'Large Desktop', 'seadev' ),
						'navbar-expand-lg'       => __( 'Desktop', 'seadev' ),
						'navbar-expand-md'       => __( 'Tablet', 'seadev' ),
						'navbar-expand-sm'       => __( 'Mobile', 'seadev' )
					),
					'priority'    => '10',
				)
			)
		);

	}
} // endif function_exists( 'seadev_theme_customize_register' ).
add_action( 'customize_register', 'seadev_theme_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
if ( ! function_exists( 'seadev_customize_preview_js' ) ) {
	/**
	 * Setup JS integration for live previewing.
	 */
	function seadev_customize_preview_js() {
		wp_enqueue_script(
			'seadev_customizer',
			get_template_directory_uri() . '/js/customizer.js',
			array( 'customize-preview' ),
			'20130508',
			true
		);
	}
}
add_action( 'customize_preview_init', 'seadev_customize_preview_js' );
