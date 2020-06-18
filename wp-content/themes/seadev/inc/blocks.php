<?php
/**
 * Manage gutenberg blocks.
 *
 * @package seadev
 */

// Create custom gutenberg block category 
function seadev_block_categories( $categories, $post ) {

	return array_merge(
			$categories,
			array(
					array(
							'slug' => 'seadev-block',
							'title' => __( 'Seadev Blocks', 'seadev' )
					)
			)
	);
}
add_filter( 'block_categories', 'seadev_block_categories', 10, 2 );


function seadev_register_acf_blocks() {
  do_action('seadev_acf_register_block_type');
}

// Check if function exists and hook into setup.
if( function_exists('acf_register_block') ) {
	add_action('acf/init', 'seadev_register_acf_blocks');
}


// Register seadev background slider block
add_action('seadev_acf_register_block_type', 'seadev_add_block_background_slider');
		function seadev_add_block_background_slider() {
	// check function exists.
	if( function_exists('acf_register_block_type') ) {
		acf_register_block_type(array(
			'name'              => 'seadev-background-slider',
			'title'             => __('Background Slider', 'seadev'),
			'description'       => __('Background Slider', 'seadev'),
			'render_template'   => get_template_directory() . '/block-templates/seadev-background-slider/seadev-background-slider.php',
			'enqueue_assets' => function(){
				wp_enqueue_style( 'seadev-background-slider', get_template_directory_uri() . '/block-templates/seadev-background-slider/seadev-background-slider.css' );
				wp_enqueue_script( 'seadev-background-slider', get_template_directory_uri() . '/block-templates/seadev-background-slider/seadev-background-slider.js', array('jquery'), null, true );
			},
			'category'          => 'seadev-block',
			'icon'              => '',
			'mode'              => 'auto',
			'keywords'          => array( 'seadev', 'section', 'background', 'slider' ),
			'supports'					=> array( 'align' => false ),
		));
	}
}
