<?php
defined( 'ABSPATH' ) || die();
require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-helper.php';

class STLSR_Setting {
	public static function add_action_links( $links ) {
		$links[] = '<a href="'. esc_url( admin_url( 'options-general.php?page=stlsr_settings' ) ) .'">' . esc_html__( 'Settings', 'login-security-recaptcha' ) . '</a>';
		return $links;
	}

	public static function save_captcha() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! isset( $_POST['save-captcha'] ) || ! wp_verify_nonce( $_POST['save-captcha'], 'save-captcha' ) ) {
			die();
		}

		$google_recaptcha_v2_site_key   = isset( $_POST['google_recaptcha_v2_site_key'] ) ? sanitize_text_field( $_POST['google_recaptcha_v2_site_key'] ) : '';
		$google_recaptcha_v2_secret_key = isset( $_POST['google_recaptcha_v2_secret_key'] ) ? sanitize_text_field( $_POST['google_recaptcha_v2_secret_key'] ) : '';
		$google_recaptcha_v2_theme      = isset( $_POST['google_recaptcha_v2_theme'] ) ? sanitize_text_field( $_POST['google_recaptcha_v2_theme'] ) : 'light';

		$google_recaptcha_v3_site_key   = isset( $_POST['google_recaptcha_v3_site_key'] ) ? sanitize_text_field( $_POST['google_recaptcha_v3_site_key'] ) : '';
		$google_recaptcha_v3_secret_key = isset( $_POST['google_recaptcha_v3_secret_key'] ) ? sanitize_text_field( $_POST['google_recaptcha_v3_secret_key'] ) : '';
		$google_recaptcha_v3_score      = isset( $_POST['google_recaptcha_v3_score'] ) ? sanitize_text_field( $_POST['google_recaptcha_v3_score'] ) : '0.3';

		$login_captcha_enable = isset( $_POST['login_captcha_enable'] ) ? (bool) $_POST['login_captcha_enable'] : false;
		$login_captcha        = isset( $_POST['login_captcha'] ) ? sanitize_text_field( $_POST['login_captcha'] ) : '';

		$lostpassword_captcha_enable = isset( $_POST['lostpassword_captcha_enable'] ) ? (bool) $_POST['lostpassword_captcha_enable'] : false;
		$lostpassword_captcha        = isset( $_POST['lostpassword_captcha'] ) ? sanitize_text_field( $_POST['lostpassword_captcha'] ) : '';

		$register_captcha_enable = isset( $_POST['register_captcha_enable'] ) ? (bool) $_POST['register_captcha_enable'] : false;
		$register_captcha        = isset( $_POST['register_captcha'] ) ? sanitize_text_field( $_POST['register_captcha'] ) : '';

		$comment_captcha_enable = isset( $_POST['comment_captcha_enable'] ) ? (bool) $_POST['comment_captcha_enable'] : false;
		$comment_captcha        = isset( $_POST['comment_captcha'] ) ? sanitize_text_field( $_POST['comment_captcha'] ) : '';

		$errors = array();

		if ( ! in_array( $google_recaptcha_v2_theme, array_keys( STLSR_Helper::google_recaptcha_v2_themes() ) ) ) {
			$google_recaptcha_v2_theme = 'light';
		}

		if ( ! in_array( $google_recaptcha_v3_score, array_keys( STLSR_Helper::google_recaptcha_v3_scores() ) ) ) {
			$google_recaptcha_v3_score = '0.3';
		}

		$captcha = array_keys( STLSR_Helper::captcha_list() );

		update_option(
			'stlsr_google_recaptcha_v2',
			array(
				'site_key'   => $google_recaptcha_v2_site_key,
				'secret_key' => $google_recaptcha_v2_secret_key,
				'theme'      => $google_recaptcha_v2_theme,
			),
			true
		);
		update_option(
			'stlsr_google_recaptcha_v3',
			array(
				'site_key'   => $google_recaptcha_v3_site_key,
				'secret_key' => $google_recaptcha_v3_secret_key,
				'score'      => $google_recaptcha_v3_score,
			),
			true
		);

		if ( $login_captcha_enable && ! in_array( $login_captcha, $captcha ) ) {
			$errors['login_captcha'] = esc_html__( 'Please select valid captcha.', 'login-security-recaptcha' );
		} else {
			update_option(
				'stlsr_login_captcha',
				array(
					'enable'  => $login_captcha_enable,
					'captcha' => $login_captcha,
				),
				true
			);
		}

		if ( $lostpassword_captcha_enable && ! in_array( $lostpassword_captcha, $captcha ) ) {
			$errors['lostpassword_captcha'] = esc_html__( 'Please select valid captcha.', 'login-security-recaptcha' );
		} else {
			update_option(
				'stlsr_lostpassword_captcha',
				array(
					'enable'  => $lostpassword_captcha_enable,
					'captcha' => $lostpassword_captcha,
				),
				true
			);
		}

		if ( $register_captcha_enable && ! in_array( $register_captcha, $captcha ) ) {
			$errors['register_captcha'] = esc_html__( 'Please select valid captcha.', 'login-security-recaptcha' );
		} else {
			update_option(
				'stlsr_register_captcha',
				array(
					'enable'  => $register_captcha_enable,
					'captcha' => $register_captcha,
				),
				true
			);
		}

		if ( $comment_captcha_enable && ! in_array( $comment_captcha, $captcha ) ) {
			$errors['comment_captcha'] = esc_html__( 'Please select valid captcha.', 'login-security-recaptcha' );
		} else {
			update_option(
				'stlsr_comment_captcha',
				array(
					'enable'  => $comment_captcha_enable,
					'captcha' => $comment_captcha,
				),
				true
			);
		}

		if ( count( $errors ) < 1 ) {
			wp_send_json_success( array( 'message' => esc_html__( 'Setting saved.', 'login-security-recaptcha' ) ) );
		}

		wp_send_json_error( $errors );
	}

	public static function reset_plugin() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! isset( $_POST['reset-plugin'] ) || ! wp_verify_nonce( $_POST['reset-plugin'], 'reset-plugin' ) ) {
			die();
		}

		delete_option( 'stlsr_google_recaptcha_v2' );
		delete_option( 'stlsr_google_recaptcha_v3' );
		delete_option( 'stlsr_login_captcha' );
		delete_option( 'stlsr_lostpassword_captcha' );
		delete_option( 'stlsr_register_captcha' );
		delete_option( 'stlsr_comment_captcha' );
		delete_option( 'stlsr_error_logs' );

		wp_send_json_success( array( 'message' => esc_html__( 'The plugin has been reset to its default state.', 'login-security-recaptcha' ) ) );
	}

	public static function clear_error_logs() {
		if ( ! current_user_can( 'manage_options' ) ) {
			die();
		}

		if ( ! isset( $_POST['clear-error-logs'] ) || ! wp_verify_nonce( $_POST['clear-error-logs'], 'clear-error-logs' ) ) {
			die();
		}

		update_option( 'stlsr_error_logs', array(), true );

		wp_send_json_success( array( 'message' => esc_html__( 'The error logs have been cleared successfully.', 'login-security-recaptcha' ) ) );
	}
}
