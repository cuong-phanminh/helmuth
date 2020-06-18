<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


// function seadev_remove_scripts() {
//     wp_dequeue_style( 'seadev-styles' );
//     wp_deregister_style( 'seadev-styles' );

//     wp_dequeue_script( 'seadev-scripts' );
//     wp_deregister_script( 'seadev-scripts' );
// }
//add_action( 'wp_enqueue_scripts', 'seadev_remove_scripts', 20 );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 9999 );
function theme_enqueue_styles() {

	// Get the theme data
	$the_theme = wp_get_theme();
    wp_enqueue_style( 'seadev-child-styles', get_stylesheet_directory_uri() . '/css/theme.min.css', array(), $the_theme->get( 'Version' ) );
    wp_enqueue_script( 'jquery');
    wp_enqueue_script( 'seadev-child-scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array(), $the_theme->get( 'Version' ), true );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

// Load custom editor style
function seadev_add_child_editor_style() {
  wp_enqueue_style('seadev-child-editor-styles', get_stylesheet_directory_uri().'/css/child-editor.css');
}
add_action('admin_enqueue_scripts', 'seadev_add_child_editor_style');


