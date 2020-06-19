<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
	// Register seadev background slider block


// add_action('seadev_acf_register_block_type', 'seadev_add_block_background_slider');
// function seadev_add_block_background_slider() {
// check function exists.
if( function_exists('acf_register_block_type') ) {
acf_register_block_type(array(
	'name'              => 'seadev-background-slider',
	'title'             => __('Background Slider', 'seadev'),
	'description'       => __('Background Slider', 'seadev'),
	'render_template'   => get_template_directory() . '/block-templates/seadev-background-slider/seadev-background-slider.php',
	'enqueue_assets' => function(){
		wp_enqueue_style( 'seadev-background-slider', get_stylesheet_directory_uri() . '/block-templates/seadev-background-slider/seadev-background-slider.css' );
		wp_enqueue_script( 'seadev-background-slider', get_stylesheet_directory_uri() . '/block-templates/seadev-background-slider/seadev-background-slider.js', array('jquery'), null, true );
	},
	'category'          => 'seadev-block',
	'icon'              => '',
	'mode'              => 'auto',
	'keywords'          => array( 'seadev', 'section', 'background', 'slider' ),
	'supports'					=> array( 'align' => false ),
));
}
}

add_action('seadev_acf_register_block_type', 'seadev_add_block_cus_background_slider');
function seadev_add_block_cus_background_slider() {
	// check function exists.
	if( function_exists('acf_register_block_type') ) {
		acf_register_block_type(array(
			'name'              => 'seadev-cus-background-slider',
			'title'             => __('Custom Background Slider', 'seadev'),
			'description'       => __('Custom Background Slider', 'seadev'),
			'render_template'   => get_stylesheet_directory() . '/block-templates/seadev-cus-background-slider/seadev-cus-background-slider.php',
			'enqueue_assets' => function(){
				wp_enqueue_style( 'seadev-cus-background-slider', get_stylesheet_directory_uri() . '/block-templates/seadev-cus-background-slider/seadev-cus-background-slider.css' );
				wp_enqueue_script( 'seadev-cus-background-slider', get_stylesheet_directory_uri() . '/block-templates/seadev-cus-background-slider/seadev-cus-background-slider.js', array('jquery'), null, true );
			},
			'category'          => 'seadev-block',
			'icon'              => '',
			'mode'              => 'auto',
			'keywords'          => array( 'seadev', 'section', 'background', 'slider' ),
			'supports'					=> array( 'align' => false ),
		));
	}
}






	





