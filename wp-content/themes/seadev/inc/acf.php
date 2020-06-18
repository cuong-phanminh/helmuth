<?php
/**
 * ACF functions.
 *
 * @package seadev
 */


add_filter('acf/settings/save_json', function() {
	return get_stylesheet_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function($paths) {
	$paths = array(get_template_directory() . '/acf-json');

	if(is_child_theme()) {
		$paths[] = get_stylesheet_directory() . '/acf-json';
	}

	return $paths;
});

/**
 * Create Seadev Theme Settings Page
 */

add_action('acf/init', 'seadev_acf_init');
function seadev_acf_init() {
	
	if( function_exists('acf_add_options_page') ) {
		
		$option_page = acf_add_options_page(array(
			'page_title' 	=> __('Advanced Theme Settings', 'seadev'),
			'menu_title' 	=> __('Advanced Theme Settings', 'seadev'),
			'menu_slug' 	=> 'seadev-advanced-theme-settings',
			'capability' 	=> 'manage_options',
			'icon_url'		=> 'dashicons-admin-customizer',
			'position'		=> 60,
			'redirect' 	=> false
		));	
	}
}

?>
