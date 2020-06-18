<?php
defined( 'ABSPATH' ) || die();

class STLSR_Admin_Menu {
	public static function create_menu() {
		$settings = add_options_page( esc_html__( 'Login Security Recaptcha', 'login-security-recaptcha' ), esc_html__( 'Login Security', 'login-security-recaptcha' ), 'manage_options', 'stlsr_settings', array( 'STLSR_Admin_Menu', 'settings' ) );
		add_action( 'admin_print_styles-' . $settings, array( 'STLSR_Admin_Menu', 'settings_assets' ) );

		$settings_submenu = add_submenu_page( 'stlsr_settings', esc_html__( 'Login Security Recaptcha', 'login-security-recaptcha' ), esc_html__( 'Login Security Recaptcha', 'login-security-recaptcha' ), 'manage_options', 'stlsr_settings', array( 'STLSR_Admin_Menu', 'settings' ) );
		add_action( 'admin_print_styles-' . $settings_submenu, array( 'STLSR_Admin_Menu', 'settings_assets' ) );
	}

	public static function settings() {
		require_once STLSR_PLUGIN_DIR_PATH . 'admin/inc/setting/index.php';
	}

	public static function settings_assets() {
		wp_enqueue_style( 'stlsr-admin', STLSR_PLUGIN_URL . 'assets/css/stlsr-admin.css', array(), '1.0.7', 'all' );
		wp_enqueue_script( 'stlsr-admin', STLSR_PLUGIN_URL . 'assets/js/stlsr-admin.js', array( 'jquery', 'jquery-form' ), '1.0.7', true );
		wp_localize_script( 'stlsr-admin', 'stlsradminurl', admin_url() );
	}
}
