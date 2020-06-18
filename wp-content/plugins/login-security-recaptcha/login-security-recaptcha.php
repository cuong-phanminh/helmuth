<?php
/**
 * Plugin Name: Login Security Recaptcha
 * Description: Secure WordPress login, registration and comment form with Google reCAPTCHA. Monitor error logs. Prevent Brute-force attack and much more.
 * Version: 1.0.7
 * Author: ScriptsTown
 * Author URI: https://scriptstown.com/
 * Text Domain: login-security-recaptcha
*/

defined( 'ABSPATH' ) || die();

if ( ! defined( 'STLSR_PLUGIN_URL' ) ) {
	define( 'STLSR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'STLSR_PLUGIN_DIR_PATH' ) ) {
	define( 'STLSR_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'STLSR_PLUGIN_BASENAME' ) ) {
	define( 'STLSR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

final class STLSR_Login_Security_Recaptcha {
	private static $instance = null;

	private function __construct() {
		$this->initialize_hooks();
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function initialize_hooks() {
		if ( is_admin() ) {
			require_once STLSR_PLUGIN_DIR_PATH . 'admin/admin.php';
		}
		require_once STLSR_PLUGIN_DIR_PATH . 'public/public.php';
	}
}
STLSR_Login_Security_Recaptcha::get_instance();
