<?php
defined( 'ABSPATH' ) || die();
require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-helper.php';

class STLSR_Captcha {
	public static function add_async_defer_attribute( $tag, $handle ) {
		if ( 'recaptcha-api' === $handle ) {
			return str_replace( ' src', ' async defer src', $tag );
		}

		return $tag;
	}

	public static function display_recaptcha( $version, $action ) {
		if ( 'google_recaptcha_v2' === $version ) {

			$google_recaptcha_v2    = get_option( 'stlsr_google_recaptcha_v2' );
			$grecaptcha_v2_site_key = isset( $google_recaptcha_v2['site_key'] ) ? esc_attr( $google_recaptcha_v2['site_key'] ) : '';
			$grecaptcha_v2_theme    = isset( $google_recaptcha_v2['theme'] ) ? esc_attr( $google_recaptcha_v2['theme'] ) : 'light';

			add_filter( 'script_loader_tag', array( 'STLSR_Captcha', 'add_async_defer_attribute' ), 10, 2 );

			wp_enqueue_style( 'stlsr', STLSR_PLUGIN_URL . 'assets/css/stlsr.css', array(), '1.0.7', 'all' );
			wp_enqueue_script( 'recaptcha-api', 'https://www.google.com/recaptcha/api.js' );
		?>
		<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $grecaptcha_v2_site_key ); ?>" data-theme="<?php echo esc_attr( $grecaptcha_v2_theme ); ?>"></div>
		<?php
		} elseif ( 'google_recaptcha_v3' === $version ) {

			$google_recaptcha_v3    = get_option( 'stlsr_google_recaptcha_v3' );
			$grecaptcha_v3_site_key = isset( $google_recaptcha_v3['site_key'] ) ? esc_attr( $google_recaptcha_v3['site_key'] ) : '';

			$script = <<<EOT
grecaptcha.ready(function() {
	grecaptcha.execute('$grecaptcha_v3_site_key', {action: '$action'}).then(function(token) {
		document.getElementById('g-recaptcha-response').value = token;
	});
});
EOT;
			wp_enqueue_script( 'recaptcha-api', 'https://www.google.com/recaptcha/api.js?render=' . $grecaptcha_v3_site_key );
			wp_add_inline_script( 'recaptcha-api', $script );
		?>
		<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
		<?php
		}
	}

	public static function login_form_captcha() {
		$login_captcha        = get_option( 'stlsr_login_captcha' );
		$login_captcha_enable = isset( $login_captcha['enable'] ) ? (bool) $login_captcha['enable'] : false;
		$login_captcha        = isset( $login_captcha['captcha'] ) ? esc_attr( $login_captcha['captcha'] ) : '';

		if ( $login_captcha_enable ) {
			self::display_recaptcha( $login_captcha, 'login' );
		}
	?>
	<?php
	}

	public static function login_verify_captcha( $user, $password ) {
		$login_captcha        = get_option( 'stlsr_login_captcha' );
		$login_captcha_enable = isset( $login_captcha['enable'] ) ? (bool) $login_captcha['enable'] : false;
		$login_captcha        = isset( $login_captcha['captcha'] ) ? esc_attr( $login_captcha['captcha'] ) : '';
		$form                 = esc_html__( 'Login', 'login-security-recaptcha' );
		$ip_address           = STLSR_Helper::get_ip_address();

		if ( $login_captcha_enable ) {
			if ( 'google_recaptcha_v2' === $login_captcha ) {
				$google_recaptcha_v2      = get_option( 'stlsr_google_recaptcha_v2' );
				$grecaptcha_v2_site_key   = isset( $google_recaptcha_v2['site_key'] ) ? esc_attr( $google_recaptcha_v2['site_key'] ) : '';
				$grecaptcha_v2_secret_key = isset( $google_recaptcha_v2['secret_key'] ) ? esc_attr( $google_recaptcha_v2['secret_key'] ) : '';

				if ( empty( $grecaptcha_v2_site_key ) || empty( $grecaptcha_v2_secret_key ) ) {
					return $user;
				}

				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
							'body' => array(
								'secret'   => $grecaptcha_v2_secret_key,
								'response' => $_POST['g-recaptcha-response'],
							)
						)
					);

					$data = wp_remote_retrieve_body( $response );
					$data = json_decode( $data );

					if ( isset( $data->{'error-codes'} ) && is_array( $data->{'error-codes'} ) && count( $data->{'error-codes'} ) ) {
						require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
						foreach ( $data->{'error-codes'} as $error_code ) {
							STLSR_Logger::log_error( $error_code, $form, $login_captcha, $ip_address );
						}

						if ( 0 !== count( array_intersect( array( 'missing-input-secret', 'invalid-input-secret' ), $data->{'error-codes'} ) ) ) {
							return $user;
						}
					}

					if ( isset( $data->success ) && true === $data->success ) {
						return $user;
					}
				}

				$error_message = wp_kses( __( '<strong>ERROR:</strong> Please confirm you are not a robot.', 'login-security-recaptcha' ), array( 'strong' => array() ) );
				return new WP_Error( 'captcha_invalid', $error_message );

			} elseif ( 'google_recaptcha_v3' === $login_captcha ) {
				$google_recaptcha_v3      = get_option( 'stlsr_google_recaptcha_v3' );
				$grecaptcha_v3_site_key   = isset( $google_recaptcha_v3['site_key'] ) ? esc_attr( $google_recaptcha_v3['site_key'] ) : '';
				$grecaptcha_v3_secret_key = isset( $google_recaptcha_v3['secret_key'] ) ? esc_attr( $google_recaptcha_v3['secret_key'] ) : '';
				$grecaptcha_v3_score      = isset( $google_recaptcha_v3['score'] ) ? esc_attr( $google_recaptcha_v3['score'] ) : '0.3';

				if ( empty( $grecaptcha_v3_site_key ) || empty( $grecaptcha_v3_secret_key ) ) {
					return $user;
				}

				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
							'body' => array(
								'secret'   => $grecaptcha_v3_secret_key,
								'response' => $_POST['g-recaptcha-response'],
								'remoteip' => $ip_address,
							)
						)
					);

					$data = wp_remote_retrieve_body( $response );
					$data = json_decode( $data );

					if ( isset( $data->{'error-codes'} ) && is_array( $data->{'error-codes'} ) && count( $data->{'error-codes'} ) ) {
						require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
						foreach ( $data->{'error-codes'} as $error_code ) {
							STLSR_Logger::log_error( $error_code, $form, $login_captcha, $ip_address );
						}

						if ( 0 !== count( array_intersect( array( 'missing-input-secret', 'invalid-input-secret' ), $data->{'error-codes'} ) ) ) {
							return $user;
						}
					}

					if ( isset( $data->success ) && true === $data->success ) {
						$grecaptcha_v3_score = (float) $grecaptcha_v3_score;
						if ( isset( $data->action ) && ( 'login' === $data->action ) && isset( $data->score ) && $data->score >= $grecaptcha_v3_score ) {
							return $user;
						} else {
							$error_code = esc_html__( 'low-score', 'login-security-recaptcha' ) . ': ' . esc_html( $data->score );
							require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
							STLSR_Logger::log_error( $error_code, $form, $login_captcha, $ip_address );
						}
					}
				}

				$error_message = wp_kses( __( '<strong>ERROR:</strong> Please confirm you are not a robot.', 'login-security-recaptcha' ), array( 'strong' => array() ) );
				return new WP_Error( 'captcha_invalid', $error_message );
			}
		}

		return $user;
	}

	public static function lostpassword_form_captcha() {
		$lostpassword_captcha        = get_option( 'stlsr_lostpassword_captcha' );
		$lostpassword_captcha_enable = isset( $lostpassword_captcha['enable'] ) ? (bool) $lostpassword_captcha['enable'] : false;
		$lostpassword_captcha        = isset( $lostpassword_captcha['captcha'] ) ? esc_attr( $lostpassword_captcha['captcha'] ) : '';

		if ( $lostpassword_captcha_enable ) {
			self::display_recaptcha( $lostpassword_captcha, 'lostpassword' );
		}
	?>
	<?php
	}

	public static function lostpassword_verify_captcha( $errors ) {
		$lostpassword_captcha        = get_option( 'stlsr_lostpassword_captcha' );
		$lostpassword_captcha_enable = isset( $lostpassword_captcha['enable'] ) ? (bool) $lostpassword_captcha['enable'] : false;
		$lostpassword_captcha        = isset( $lostpassword_captcha['captcha'] ) ? esc_attr( $lostpassword_captcha['captcha'] ) : '';
		$form                        = esc_html__( 'Lost Password', 'login-security-recaptcha' );
		$ip_address                  = STLSR_Helper::get_ip_address();

		if ( $lostpassword_captcha_enable ) {
			if ( 'google_recaptcha_v2' === $lostpassword_captcha ) {
				$google_recaptcha_v2      = get_option( 'stlsr_google_recaptcha_v2' );
				$grecaptcha_v2_site_key   = isset( $google_recaptcha_v2['site_key'] ) ? esc_attr( $google_recaptcha_v2['site_key'] ) : '';
				$grecaptcha_v2_secret_key = isset( $google_recaptcha_v2['secret_key'] ) ? esc_attr( $google_recaptcha_v2['secret_key'] ) : '';

				if ( empty( $grecaptcha_v2_site_key ) || empty( $grecaptcha_v2_secret_key ) ) {
					return $errors;
				}

				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
							'body' => array(
								'secret'   => $grecaptcha_v2_secret_key,
								'response' => $_POST['g-recaptcha-response'],
							)
						)
					);

					$data = wp_remote_retrieve_body( $response );
					$data = json_decode( $data );

					if ( isset( $data->{'error-codes'} ) && is_array( $data->{'error-codes'} ) && count( $data->{'error-codes'} ) ) {
						require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
						foreach ( $data->{'error-codes'} as $error_code ) {
							STLSR_Logger::log_error( $error_code, $form, $lostpassword_captcha, $ip_address );
						}

						if ( 0 !== count( array_intersect( array( 'missing-input-secret', 'invalid-input-secret' ), $data->{'error-codes'} ) ) ) {
							return $errors;
						}
					}

					if ( isset( $data->success ) && true === $data->success ) {
						return $errors;
					}
				}

				$error_message = wp_kses( __( '<strong>ERROR:</strong> Please confirm you are not a robot.', 'login-security-recaptcha' ), array( 'strong' => array() ) );
				$errors->add( 'captcha_invalid', $error_message );

			} elseif ( 'google_recaptcha_v3' === $lostpassword_captcha ) {
				$google_recaptcha_v3      = get_option( 'stlsr_google_recaptcha_v3' );
				$grecaptcha_v3_site_key   = isset( $google_recaptcha_v3['site_key'] ) ? esc_attr( $google_recaptcha_v3['site_key'] ) : '';
				$grecaptcha_v3_secret_key = isset( $google_recaptcha_v3['secret_key'] ) ? esc_attr( $google_recaptcha_v3['secret_key'] ) : '';
				$grecaptcha_v3_score      = isset( $google_recaptcha_v3['score'] ) ? esc_attr( $google_recaptcha_v3['score'] ) : '0.3';

				if ( empty( $grecaptcha_v3_site_key ) || empty( $grecaptcha_v3_secret_key ) ) {
					return $errors;
				}

				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
							'body' => array(
								'secret'   => $grecaptcha_v3_secret_key,
								'response' => $_POST['g-recaptcha-response'],
								'remoteip' => $ip_address,
							)
						)
					);

					$data = wp_remote_retrieve_body( $response );
					$data = json_decode( $data );

					if ( isset( $data->{'error-codes'} ) && is_array( $data->{'error-codes'} ) && count( $data->{'error-codes'} ) ) {
						require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
						foreach ( $data->{'error-codes'} as $error_code ) {
							STLSR_Logger::log_error( $error_code, $form, $lostpassword_captcha, $ip_address );
						}

						if ( 0 !== count( array_intersect( array( 'missing-input-secret', 'invalid-input-secret' ), $data->{'error-codes'} ) ) ) {
							return $errors;
						}
					}

					if ( isset( $data->success ) && true === $data->success ) {
						$grecaptcha_v3_score = (float) $grecaptcha_v3_score;
						if ( isset( $data->action ) && ( 'lostpassword' === $data->action ) && isset( $data->score ) && $data->score >= $grecaptcha_v3_score ) {
							return $errors;
						} else {
							$error_code = esc_html__( 'low-score', 'login-security-recaptcha' ) . ': ' . esc_html( $data->score );
							require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
							STLSR_Logger::log_error( $error_code, $form, $login_captcha, $ip_address );
						}
					}
				}

				$error_message = wp_kses( __( '<strong>ERROR:</strong> Please confirm you are not a robot.', 'login-security-recaptcha' ), array( 'strong' => array() ) );
				$errors->add( 'captcha_invalid', $error_message );
			}
		}

		return $errors;
	}

	public static function register_form_captcha() {
		$register_captcha        = get_option( 'stlsr_register_captcha' );
		$register_captcha_enable = isset( $register_captcha['enable'] ) ? (bool) $register_captcha['enable'] : false;
		$register_captcha        = isset( $register_captcha['captcha'] ) ? esc_attr( $register_captcha['captcha'] ) : '';

		if ( $register_captcha_enable ) {
			self::display_recaptcha( $register_captcha, 'register' );
		}
	?>
	<?php
	}

	public static function register_verify_captcha( $errors, $sanitized_user_login, $user_email ) {
		$register_captcha        = get_option( 'stlsr_register_captcha' );
		$register_captcha_enable = isset( $register_captcha['enable'] ) ? (bool) $register_captcha['enable'] : false;
		$register_captcha        = isset( $register_captcha['captcha'] ) ? esc_attr( $register_captcha['captcha'] ) : '';
		$form                    = esc_html__( 'Register', 'login-security-recaptcha' );
		$ip_address              = STLSR_Helper::get_ip_address();

		if ( $register_captcha_enable ) {
			if ( 'google_recaptcha_v2' === $register_captcha ) {
				$google_recaptcha_v2      = get_option( 'stlsr_google_recaptcha_v2' );
				$grecaptcha_v2_site_key   = isset( $google_recaptcha_v2['site_key'] ) ? esc_attr( $google_recaptcha_v2['site_key'] ) : '';
				$grecaptcha_v2_secret_key = isset( $google_recaptcha_v2['secret_key'] ) ? esc_attr( $google_recaptcha_v2['secret_key'] ) : '';

				if ( empty( $grecaptcha_v2_site_key ) || empty( $grecaptcha_v2_secret_key ) ) {
					return $errors;
				}

				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
							'body' => array(
								'secret'   => $grecaptcha_v2_secret_key,
								'response' => $_POST['g-recaptcha-response'],
							)
						)
					);

					$data = wp_remote_retrieve_body( $response );
					$data = json_decode( $data );

					if ( isset( $data->{'error-codes'} ) && is_array( $data->{'error-codes'} ) && count( $data->{'error-codes'} ) ) {
						require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
						foreach ( $data->{'error-codes'} as $error_code ) {
							STLSR_Logger::log_error( $error_code, $form, $register_captcha, $ip_address );
						}

						if ( 0 !== count( array_intersect( array( 'missing-input-secret', 'invalid-input-secret' ), $data->{'error-codes'} ) ) ) {
							return $errors;
						}
					}

					if ( isset( $data->success ) && true === $data->success ) {
						return $errors;
					}
				}

				$error_message = wp_kses( __( '<strong>ERROR:</strong> Please confirm you are not a robot.', 'login-security-recaptcha' ), array( 'strong' => array() ) );
				$errors->add( 'captcha_invalid', $error_message );

			} elseif ( 'google_recaptcha_v3' === $register_captcha ) {
				$google_recaptcha_v3      = get_option( 'stlsr_google_recaptcha_v3' );
				$grecaptcha_v3_site_key   = isset( $google_recaptcha_v3['site_key'] ) ? esc_attr( $google_recaptcha_v3['site_key'] ) : '';
				$grecaptcha_v3_secret_key = isset( $google_recaptcha_v3['secret_key'] ) ? esc_attr( $google_recaptcha_v3['secret_key'] ) : '';
				$grecaptcha_v3_score      = isset( $google_recaptcha_v3['score'] ) ? esc_attr( $google_recaptcha_v3['score'] ) : '0.3';

				if ( empty( $grecaptcha_v3_site_key ) || empty( $grecaptcha_v3_secret_key ) ) {
					return $errors;
				}

				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
							'body' => array(
								'secret'   => $grecaptcha_v3_secret_key,
								'response' => $_POST['g-recaptcha-response'],
								'remoteip' => $ip_address,
							)
						)
					);

					$data = wp_remote_retrieve_body( $response );
					$data = json_decode( $data );

					if ( isset( $data->{'error-codes'} ) && is_array( $data->{'error-codes'} ) && count( $data->{'error-codes'} ) ) {
						require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
						foreach ( $data->{'error-codes'} as $error_code ) {
							STLSR_Logger::log_error( $error_code, $form, $register_captcha, $ip_address );
						}

						if ( 0 !== count( array_intersect( array( 'missing-input-secret', 'invalid-input-secret' ), $data->{'error-codes'} ) ) ) {
							return $errors;
						}
					}

					if ( isset( $data->success ) && true === $data->success ) {
						$grecaptcha_v3_score = (float) $grecaptcha_v3_score;
						if ( isset( $data->action ) && ( 'register' === $data->action ) && isset( $data->score ) && $data->score >= $grecaptcha_v3_score ) {
							return $errors;
						} else {
							$error_code = esc_html__( 'low-score', 'login-security-recaptcha' ) . ': ' . esc_html( $data->score );
							require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
							STLSR_Logger::log_error( $error_code, $form, $login_captcha, $ip_address );
						}
					}
				}

				$error_message = wp_kses( __( '<strong>ERROR:</strong> Please confirm you are not a robot.', 'login-security-recaptcha' ), array( 'strong' => array() ) );
				$errors->add( 'captcha_invalid', $error_message );
			}
		}

		return $errors;
	}

	public static function comment_form_captcha() {
		$comment_captcha        = get_option( 'stlsr_comment_captcha' );
		$comment_captcha_enable = isset( $comment_captcha['enable'] ) ? (bool) $comment_captcha['enable'] : false;
		$comment_captcha        = isset( $comment_captcha['captcha'] ) ? esc_attr( $comment_captcha['captcha'] ) : '';

		if ( $comment_captcha_enable ) {
			self::display_recaptcha( $comment_captcha, 'comment' );
		}
	?>
	<?php
	}

	public static function comment_verify_captcha( $commentdata ) {
		if ( is_user_logged_in() ) {
			return $commentdata;
		}

		$comment_captcha        = get_option( 'stlsr_comment_captcha' );
		$comment_captcha_enable = isset( $comment_captcha['enable'] ) ? (bool) $comment_captcha['enable'] : false;
		$comment_captcha        = isset( $comment_captcha['captcha'] ) ? esc_attr( $comment_captcha['captcha'] ) : '';
		$form                   = esc_html__( 'Comment', 'login-security-recaptcha' );
		$ip_address             = STLSR_Helper::get_ip_address();

		if ( $comment_captcha_enable ) {
			if ( 'google_recaptcha_v2' === $comment_captcha ) {
				$google_recaptcha_v2      = get_option( 'stlsr_google_recaptcha_v2' );
				$grecaptcha_v2_site_key   = isset( $google_recaptcha_v2['site_key'] ) ? esc_attr( $google_recaptcha_v2['site_key'] ) : '';
				$grecaptcha_v2_secret_key = isset( $google_recaptcha_v2['secret_key'] ) ? esc_attr( $google_recaptcha_v2['secret_key'] ) : '';

				if ( empty( $grecaptcha_v2_site_key ) || empty( $grecaptcha_v2_secret_key ) ) {
					return $commentdata;
				}

				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
							'body' => array(
								'secret'   => $grecaptcha_v2_secret_key,
								'response' => $_POST['g-recaptcha-response'],
							)
						)
					);

					$data = wp_remote_retrieve_body( $response );
					$data = json_decode( $data );

					if ( isset( $data->{'error-codes'} ) && is_array( $data->{'error-codes'} ) && count( $data->{'error-codes'} ) ) {
						require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
						foreach ( $data->{'error-codes'} as $error_code ) {
							STLSR_Logger::log_error( $error_code, $form, $comment_captcha, $ip_address );
						}

						if ( 0 !== count( array_intersect( array( 'missing-input-secret', 'invalid-input-secret' ), $data->{'error-codes'} ) ) ) {
							return $commentdata;
						}
					}

					if ( isset( $data->success ) && true === $data->success ) {
						return $commentdata;
					}
				}

				$error_message = wp_kses( __( '<strong>ERROR:</strong> Please confirm you are not a robot.', 'login-security-recaptcha' ), array( 'strong' => array() ) );
				wp_die( $error_message, '', array( 'back_link' => true ) );

			} elseif ( 'google_recaptcha_v3' === $comment_captcha ) {
				$google_recaptcha_v3      = get_option( 'stlsr_google_recaptcha_v3' );
				$grecaptcha_v3_site_key   = isset( $google_recaptcha_v3['site_key'] ) ? esc_attr( $google_recaptcha_v3['site_key'] ) : '';
				$grecaptcha_v3_secret_key = isset( $google_recaptcha_v3['secret_key'] ) ? esc_attr( $google_recaptcha_v3['secret_key'] ) : '';
				$grecaptcha_v3_score      = isset( $google_recaptcha_v3['score'] ) ? esc_attr( $google_recaptcha_v3['score'] ) : '0.3';

				if ( empty( $grecaptcha_v3_site_key ) || empty( $grecaptcha_v3_secret_key ) ) {
					return $commentdata;
				}

				if ( isset( $_POST['g-recaptcha-response'] ) ) {
					$response = wp_remote_post(
						'https://www.google.com/recaptcha/api/siteverify',
						array(
							'body' => array(
								'secret'   => $grecaptcha_v3_secret_key,
								'response' => $_POST['g-recaptcha-response'],
								'remoteip' => $ip_address,
							)
						)
					);

					$data = wp_remote_retrieve_body( $response );
					$data = json_decode( $data );

					if ( isset( $data->{'error-codes'} ) && is_array( $data->{'error-codes'} ) && count( $data->{'error-codes'} ) ) {
						require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
						foreach ( $data->{'error-codes'} as $error_code ) {
							STLSR_Logger::log_error( $error_code, $form, $comment_captcha, $ip_address );
						}

						if ( 0 !== count( array_intersect( array( 'missing-input-secret', 'invalid-input-secret' ), $data->{'error-codes'} ) ) ) {
							return $commentdata;
						}
					}

					if ( isset( $data->success ) && true === $data->success ) {
						$grecaptcha_v3_score = (float) $grecaptcha_v3_score;
						if ( isset( $data->action ) && ( 'comment' === $data->action ) && isset( $data->score ) && $data->score >= $grecaptcha_v3_score ) {
							return $commentdata;
						} else {
							$error_code = esc_html__( 'low-score', 'login-security-recaptcha' ) . ': ' . esc_html( $data->score );
							require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-logger.php';
							STLSR_Logger::log_error( $error_code, $form, $login_captcha, $ip_address );
						}
					}
				}

				$error_message = wp_kses( __( '<strong>ERROR:</strong> Please confirm you are not a robot.', 'login-security-recaptcha' ), array( 'strong' => array() ) );
				wp_die( $error_message, '', array( 'back_link' => true ) );
			}
		}

		return $commentdata;
	}
}
