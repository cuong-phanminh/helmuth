<?php
defined( 'ABSPATH' ) || die();

class STLSR_Helper {
	public static function captcha_list() {
		return array(
			'google_recaptcha_v2'  => esc_html__( 'Google reCAPTCHA Version 2', 'login-security-recaptcha' ),
			'google_recaptcha_v3'  => esc_html__( 'Google reCAPTCHA Version 3', 'login-security-recaptcha' ),
		);
	}

	public static function google_recaptcha_v2_themes() {
		return array(
			'light' => esc_html__( 'Light', 'login-security-recaptcha' ),
			'dark'  => esc_html__( 'Dark', 'login-security-recaptcha' ),
		);
	}

	public static function google_recaptcha_v3_scores() {
		return array(
			'0.1' => esc_html__( '0.1', 'login-security-recaptcha' ),
			'0.2' => esc_html__( '0.2', 'login-security-recaptcha' ),
			'0.3' => esc_html__( '0.3', 'login-security-recaptcha' ),
			'0.4' => esc_html__( '0.4', 'login-security-recaptcha' ),
			'0.5' => esc_html__( '0.5', 'login-security-recaptcha' ),
		);
	}

	public static function get_steps_url() {
		return 'https://scriptstown.com/how-to-get-site-and-secret-key-for-google-recaptcha/';
	}

	public static function get_ip_address() {
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		}

		$ip = filter_var( $ip, FILTER_VALIDATE_IP );
		$ip = ( false === $ip ) ? '0.0.0.0' : $ip;

		return $ip;
	}
}
