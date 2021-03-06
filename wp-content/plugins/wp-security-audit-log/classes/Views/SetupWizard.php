<?php
/**
 * View: WSAL Setup
 *
 * WSAL setup class file.
 *
 * @since 3.2.3
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class: WSAL Setup Wizard.
 *
 * WSAL setup wizard class which manages the functionality
 * related to setup.
 */
final class WSAL_Views_SetupWizard {

	/**
	 * Instance of WpSecurityAuditLog
	 *
	 * @var WpSecurityAuditLog
	 */
	private $wsal;

	/**
	 * Wizard Steps
	 *
	 * @var array
	 */
	private $wizard_steps;

	/**
	 * Current Step
	 *
	 * @var string
	 */
	private $current_step;

	/**
	 * Method: Constructor.
	 *
	 * @param WpSecurityAuditLog $wsal – Instance of main plugin.
	 */
	public function __construct( WpSecurityAuditLog $wsal ) {
		$this->wsal = $wsal;

		add_action( 'admin_menu', array( $this, 'admin_menus' ), 10 );
		add_action( 'admin_init', array( $this, 'setup_page' ), 10 );
		add_action( 'wp_ajax_setup_check_security_token', array( $this, 'setup_check_security_token' ) );
	}

	/**
	 * Ajax handler to verify setting token.
	 */
	public function setup_check_security_token() {
		if ( ! $this->wsal->settings->CurrentUserCan( 'view' ) ) {
			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Access Denied.', 'wp-security-audit-log' ),
				)
			);
			die();
		}

		//@codingStandardsIgnoreStart
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : false;
		$token = isset( $_POST['token'] ) ? sanitize_text_field( $_POST['token'] ) : false;
		//@codingStandardsIgnoreEnd

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wsal-verify-wizard-page' ) ) {
			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Nonce verification failed.', 'wp-security-audit-log' ),
				)
			);
			die();
		}

		if ( empty( $token ) ) {
			echo wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Invalid input.', 'wp-security-audit-log' ),
				)
			);
			die();
		}

		echo wp_json_encode(
			array(
				'success'   => true,
				'token'     => $token,
				'tokenType' => esc_html( $this->get_token_type( $token ) ),
			)
		);
		die();
	}

	/**
	 * Add setup admin page.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'wsal-setup', '' );
	}

	/**
	 * Setup Page Start.
	 */
	public function setup_page() {
		// Get page argument from $_GET array.
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
		if ( empty( $page ) || 'wsal-setup' !== $page ) {
			return;
		}

		/**
		 * Wizard Steps.
		 */
		$wizard_steps = array(
			'welcome'        => array(
				'name'    => __( 'Welcome', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_welcome' ),
			),
			'log_details'    => array(
				'name'    => __( 'Log Details', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_log_details' ),
				'save'    => array( $this, 'wsal_step_log_details_save' ),
			),
			'login'          => array(
				'name'    => __( 'Log In', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_login' ),
				'save'    => array( $this, 'wsal_step_login_save' ),
			),
			'404s'           => array(
				'name'    => __( '404s', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_404s' ),
				'save'    => array( $this, 'wsal_step_404s_save' ),
			),
			'register'       => array(
				'name'    => __( 'User Registrations', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_register' ),
				'save'    => array( $this, 'wsal_step_register_save' ),
			),
			'log_retention'  => array(
				'name'    => __( 'Log Retention', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_log_retention' ),
				'save'    => array( $this, 'wsal_step_log_retention_save' ),
			),
			'access'         => array(
				'name'    => __( 'Access', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_access' ),
				'save'    => array( $this, 'wsal_step_access_save' ),
			),
			'exclude_object' => array(
				'name'    => __( 'Exclude Objects', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_exclude_object' ),
				'save'    => array( $this, 'wsal_step_exclude_object_save' ),
			),
			'finish'         => array(
				'name'    => __( 'Finish', 'wp-security-audit-log' ),
				'content' => array( $this, 'wsal_step_finish' ),
				'save'    => array( $this, 'wsal_step_finish_save' ),
			),
		);

		/**
		 * Filter: `Wizard Default Steps`
		 *
		 * WSAL filter to filter wizard steps before they are displayed.
		 *
		 * @param array $wizard_steps – Wizard Steps.
		 */
		$this->wizard_steps = apply_filters( 'wsal_wizard_default_steps', $wizard_steps );

		// Set current step.
		$current_step       = filter_input( INPUT_GET, 'current-step', FILTER_SANITIZE_STRING );
		$this->current_step = ! empty( $current_step ) ? $current_step : current( array_keys( $this->wizard_steps ) );

		/**
		 * Enqueue Styles.
		 */
		wp_enqueue_style(
			'wsal-wizard-css',
			$this->wsal->GetBaseUrl() . '/css/dist/wsal-wizard.build.css',
			array( 'dashicons', 'install', 'forms' ),
			filemtime( $this->wsal->GetBaseDir() . 'css/dist/wsal-wizard.build.css' )
		);

		/**
		 * Enqueue Scripts.
		 */
		wp_register_script(
			'wsal-wizard-js',
			$this->wsal->GetBaseUrl() . '/js/dist/wsal-wizard.min.js',
			array(),
			filemtime( $this->wsal->GetBaseDir() . 'js/dist/wsal-wizard.min.js' ),
			false
		);

		// Data array.
		$data_array = array(
			'ajaxURL'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'wsal-verify-wizard-page' ),
			'usersError' => esc_html__( 'Specified value in not a user.', 'wp-security-audit-log' ),
			'rolesError' => esc_html__( 'Specified value in not a role.', 'wp-security-audit-log' ),
			'ipError'    => esc_html__( 'Specified value in not an IP address.', 'wp-security-audit-log' ),
		);
		wp_localize_script( 'wsal-wizard-js', 'wsalData', $data_array );

		/**
		 * Save Wizard Settings.
		 */
		$save_step = filter_input( INPUT_POST, 'save_step', FILTER_SANITIZE_STRING );
		if ( ! empty( $save_step ) && ! empty( $this->wizard_steps[ $this->current_step ]['save'] ) ) {
			call_user_func( $this->wizard_steps[ $this->current_step ]['save'] );
		}

		ob_start();
		$this->setup_page_header();
		$this->setup_page_steps();
		$this->setup_page_content();
		$this->setup_page_footer();
		exit;
	}

	/**
	 * Setup Page Header.
	 */
	private function setup_page_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'WP Security Audit Log &rsaquo; Setup Wizard', 'wp-security-audit-log' ); ?></title>
			<?php wp_print_scripts( 'wsal-wizard-js' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="wsal-setup wp-core-ui">
			<h1 id="wsal-logo"><a href="https://wpsecurityauditlog.com/?utm_source=plugin&utm_medium=referral&utm_campaign=WSAL&utm_content=wizard+configuration" target="_blank"><img src="<?php echo esc_url( $this->wsal->GetBaseUrl() ); ?>/img/wsal-logo-full.png" alt="WP Security Audit Log" /></a></h1>
		<?php
	}

	/**
	 * Setup Page Footer.
	 */
	private function setup_page_footer() {
		?>
			<div class="wsal-setup-footer">
				<?php if ( 'welcome' !== $this->current_step && 'finish' !== $this->current_step ) : // Don't show the link on the first & last step. ?>
					<a href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Close Wizard', 'wp-security-audit-log' ); ?></a>
				<?php endif; ?>
			</div>
			</body>
		</html>
		<?php
	}

	/**
	 * Setup Page Steps.
	 */
	private function setup_page_steps() {
		?>
		<ul class="steps">
			<?php
			foreach ( $this->wizard_steps as $key => $step ) :
				if ( $key === $this->current_step ) :
					?>
					<li class="is-active"><?php echo esc_html( $step['name'] ); ?></li>
				<?php else : ?>
					<li></li>
					<?php
				endif;
			endforeach;
			?>
		</ul>
		<?php
	}

	/**
	 * Get Next Step URL.
	 *
	 * @return string
	 */
	private function get_next_step() {
		// Get current step.
		$current_step = $this->current_step;

		// Array of step keys.
		$keys = array_keys( $this->wizard_steps );
		if ( end( $keys ) === $current_step ) { // If last step is active then return WP Admin URL.
			return admin_url();
		}

		// Search for step index in step keys.
		$step_index = array_search( $current_step, $keys, true );
		if ( false === $step_index ) { // If index is not found then return empty string.
			return '';
		}

		// Return next step.
		return add_query_arg( 'current-step', $keys[ $step_index + 1 ] );
	}

	/**
	 * Setup Page Content.
	 */
	private function setup_page_content() {
		?>
		<div class="wsal-setup-content">
			<?php
			if ( ! empty( $this->wizard_steps[ $this->current_step ]['content'] ) ) {
				call_user_func( $this->wizard_steps[ $this->current_step ]['content'] );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Step View: `Welcome`
	 */
	private function wsal_step_welcome() {
		// Dismiss the setup modal on audit log.
		if ( 'no' === $this->wsal->GetGlobalOption( 'wsal-setup-modal-dismissed', 'no' ) ) {
			$this->wsal->SetGlobalOption( 'wsal-setup-modal-dismissed', 'yes' );
		}
		?>
		<p><?php esc_html_e( 'This wizard helps you configure the basic plugin settings. All these settings can be changed at a later stage from the plugin settings.', 'wp-security-audit-log' ); ?></p>

		<div class="wsal-setup-actions">
			<a class="button button-primary"
				href="<?php echo esc_url( $this->get_next_step() ); ?>">
				<?php esc_html_e( 'Start Configuring the Plugin', 'wp-security-audit-log' ); ?>
			</a>
			<a class="button button-secondary"
				href="<?php echo esc_url( admin_url() ); ?>">
				<?php esc_html_e( 'Exit Wizard', 'wp-security-audit-log' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Step View: `Log Details`
	 */
	private function wsal_step_log_details() {
		?>
		<form method="post" class="wsal-setup-form">
			<?php wp_nonce_field( 'wsal-step-log-details' ); ?>
			<h4><?php esc_html_e( 'Please select the level of detail for your WordPress activity logs:', 'wp-security-audit-log' ); ?></h4>
			<fieldset>
				<label for="basic">
					<input id="basic" name="wsal-details-level" type="radio" value="basic">
					<?php esc_html_e( 'Basic (I want a high level overview and I am not interested in the detail)', 'wp-security-audit-log' ); ?>
				</label>
				<br />
				<label for="geek">
					<input id="geek" name="wsal-details-level" type="radio" value="geek" checked>
					<?php esc_html_e( 'Geek (I want to know everything that is happening on my WordPress)', 'wp-security-audit-log' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Note: You can change the WordPress logging level from the plugin’s settings anytime.', 'wp-security-audit-log' ); ?></p>
			</fieldset>
			<div class="wsal-setup-actions">
				<button class="button button-primary" type="submit" name="save_step" value="<?php esc_attr_e( 'Next', 'wp-security-audit-log' ); ?>"><?php esc_html_e( 'Next', 'wp-security-audit-log' ); ?></button>
			</div>
		</form>
		<?php
	}

	/**
	 * Step Save: `Log Details`
	 */
	private function wsal_step_log_details_save() {
		// Check nonce.
		check_admin_referer( 'wsal-step-log-details' );

		// Save Log Details Step setting.
		// @codingStandardsIgnoreStart
		$log_details = isset( $_POST['wsal-details-level'] ) ? sanitize_text_field( $_POST['wsal-details-level'] ) : false;
		// @codingStandardsIgnoreEnd

		// Save log details option.
		$this->wsal->SetGlobalOption( 'details-level', $log_details );
		if ( ! empty( $log_details ) && 'basic' === $log_details ) {
			$this->wsal->settings->set_basic_mode();
		} elseif ( ! empty( $log_details ) && 'geek' === $log_details ) {
			$this->wsal->settings->set_geek_mode();
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step() ) );
		exit();
	}

	/**
	 * Step View: `Login Sensor`
	 */
	private function wsal_step_login() {
		?>
		<form method="post" class="wsal-setup-form">
			<?php wp_nonce_field( 'wsal-step-login' ); ?>
			<h4><?php esc_html_e( 'Do you or your users use other pages to log in to WordPress other than the default login page ( /wp-admin/ )?', 'wp-security-audit-log' ); ?></h4>
			<fieldset>
				<label for="wsal-frontend-events-login-yes">
					<input id="wsal-frontend-events-login-yes" name="wsal-frontend-login" type="radio" value="1">
					<?php esc_html_e( 'Yes, we use other pages to login to WordPress.', 'wp-security-audit-log' ); ?>
				</label>
				<br />
				<label for="wsal-frontend-events-login-no">
					<input id="wsal-frontend-events-login-no" name="wsal-frontend-login" type="radio" value="0" checked>
					<?php esc_html_e( 'No, we only use the default WordPress login page.', 'wp-security-audit-log' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'If your website is a membership or ecommerce website most probably you have more than one area from where the users can login. If you are not sure, select Yes.', 'wp-security-audit-log' ); ?></p>
			</fieldset>
			<!-- Question -->
			<p class="description"><?php esc_html_e( 'Note: You can change the WordPress activity log retention settings at any time from the plugin settings later on.', 'wp-security-audit-log' ); ?></p>
			<div class="wsal-setup-actions">
				<button class="button button-primary" type="submit" name="save_step" value="<?php esc_attr_e( 'Next', 'wp-security-audit-log' ); ?>"><?php esc_html_e( 'Next', 'wp-security-audit-log' ); ?></button>
			</div>
		</form>
		<?php
	}

	/**
	 * Step Save: `Login Sensor`
	 */
	private function wsal_step_login_save() {
		// Check nonce.
		check_admin_referer( 'wsal-step-login' );

		if ( isset( $_POST['wsal-frontend-login'] ) ) {
			$frontend_sensors = $this->wsal->settings->get_frontend_events(); // Get the frontend sensors setting.
			$login_sensor     = sanitize_text_field( wp_unslash( $_POST['wsal-frontend-login'] ) );
			$login_sensor     = '0' === $login_sensor ? false : $login_sensor; // Update the sensor option.

			$frontend_sensors['login'] = $login_sensor;
			$this->wsal->settings->set_frontend_events( $frontend_sensors );
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step() ) );
		exit();
	}

	/**
	 * Step View: `404s Sensor`
	 */
	private function wsal_step_404s() {
		?>
		<form method="post" class="wsal-setup-form">
			<?php wp_nonce_field( 'wsal-step-404s' ); ?>
			<h4><?php esc_html_e( 'Do you want to keep a log of (non-logged in) visitors’ requests to non-existing URLs which generate a HTTP 404 error response?', 'wp-security-audit-log' ); ?></h4>
			<fieldset>
				<label for="wsal-frontend-events-system-yes">
					<input id="wsal-frontend-events-system-yes" name="wsal-frontend-system" type="radio" value="1">
					<?php esc_html_e( 'Yes', 'wp-security-audit-log' ); ?>
				</label>
				<br />
				<label for="wsal-frontend-events-system-no">
					<input id="wsal-frontend-events-system-no" name="wsal-frontend-system" type="radio" value="0" checked>
					<?php esc_html_e( 'No', 'wp-security-audit-log' ); ?>
				</label>
			</fieldset>
			<!-- Question -->
			<p class="description"><?php esc_html_e( 'Note: You can change the WordPress activity log retention settings at any time from the plugin settings later on.', 'wp-security-audit-log' ); ?></p>
			<div class="wsal-setup-actions">
				<button class="button button-primary" type="submit" name="save_step" value="<?php esc_attr_e( 'Next', 'wp-security-audit-log' ); ?>"><?php esc_html_e( 'Next', 'wp-security-audit-log' ); ?></button>
			</div>
		</form>
		<?php
	}

	/**
	 * Step Save: `404s Sensor`
	 */
	private function wsal_step_404s_save() {
		// Check nonce.
		check_admin_referer( 'wsal-step-404s' );

		if ( isset( $_POST['wsal-frontend-system'] ) ) {
			$frontend_sensors = $this->wsal->settings->get_frontend_events(); // Get the frontend sensors setting.
			$system_sensor    = sanitize_text_field( wp_unslash( $_POST['wsal-frontend-system'] ) );
			$system_sensor    = '0' === $system_sensor ? false : $system_sensor; // Update the sensor option.

			$frontend_sensors['system'] = $system_sensor;
			$this->wsal->settings->set_frontend_events( $frontend_sensors );
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step() ) );
		exit();
	}

	/**
	 * Step View: `Register Sensor`
	 */
	private function wsal_step_register() {
		?>
		<form method="post" class="wsal-setup-form">
			<?php wp_nonce_field( 'wsal-step-frontend-register' ); ?>
			<h4><?php esc_html_e( 'Can visitors register for a user on your website?', 'wp-security-audit-log' ); ?></h4>
			<fieldset>
				<label for="wsal-frontend-events-register-yes">
					<input id="wsal-frontend-events-register-yes" name="wsal-frontend-register" type="radio" value="1">
					<?php esc_html_e( 'Yes', 'wp-security-audit-log' ); ?>
				</label>
				<br />
				<label for="wsal-frontend-events-register-no">
					<input id="wsal-frontend-events-register-no" name="wsal-frontend-register" type="radio" value="0" checked>
					<?php esc_html_e( 'No', 'wp-security-audit-log' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'If you are not sure about this setting, check if the Membership setting in the WordPress General settings is checked or not. If it is not checked (default) select No.', 'wp-security-audit-log' ); ?></p>
			</fieldset>
			<!-- Question -->
			<p class="description"><?php esc_html_e( 'Note: You can change the WordPress activity log retention settings at any time from the plugin settings later on.', 'wp-security-audit-log' ); ?></p>
			<div class="wsal-setup-actions">
				<button class="button button-primary" type="submit" name="save_step" value="<?php esc_attr_e( 'Next', 'wp-security-audit-log' ); ?>"><?php esc_html_e( 'Next', 'wp-security-audit-log' ); ?></button>
			</div>
		</form>
		<?php
	}

	/**
	 * Step Save: `Register Sensor`
	 */
	private function wsal_step_register_save() {
		// Check nonce.
		check_admin_referer( 'wsal-step-frontend-register' );

		if ( isset( $_POST['wsal-frontend-register'] ) ) {
			$frontend_sensors = $this->wsal->settings->get_frontend_events(); // Get the frontend sensors setting.
			$register_sensor  = sanitize_text_field( wp_unslash( $_POST['wsal-frontend-register'] ) );
			$register_sensor  = '0' === $register_sensor ? false : $register_sensor; // Update the sensor option.

			$frontend_sensors['register'] = $register_sensor;
			$this->wsal->settings->set_frontend_events( $frontend_sensors );
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step() ) );
		exit();
	}

	/**
	 * Step View: `Log Retention`
	 */
	private function wsal_step_log_retention() {
		?>
		<form method="post" class="wsal-setup-form">
			<?php wp_nonce_field( 'wsal-step-log-retention' ); ?>
			<h4>
				<?php esc_html_e( 'How long do you want to keep the data in the WordPress activity Log?', 'wp-security-audit-log' ); ?>
			</h4>
			<fieldset>
				<label for="6">
					<input id="6" name="wsal-pruning-limit" type="radio" value="6" checked />
					<?php esc_html_e( '6 months (data older than 6 months will be deleted)', 'wp-security-audit-log' ); ?>
				</label>
				<br />
				<label for="12">
					<input id="12" name="wsal-pruning-limit" type="radio" value="12" />
					<?php esc_html_e( '12 months (data older than 12 months will be deleted)', 'wp-security-audit-log' ); ?>
				</label>
				<br />
				<label for="none">
					<input id="none" name="wsal-pruning-limit" type="radio" value="none" />
					<?php esc_html_e( 'Keep all data.', 'wp-security-audit-log' ); ?>
				</label>
				<p class="description">
					<?php esc_html_e( 'Note: You can change the WordPress activity log retention settings at any time from the plugin settings later on.', 'wp-security-audit-log' ); ?>
				</p>
			</fieldset>

			<div class="wsal-setup-actions">
				<button class="button button-primary"
					type="submit"
					name="save_step"
					value="<?php esc_attr_e( 'Next', 'wp-security-audit-log' ); ?>">
					<?php esc_html_e( 'Next', 'wp-security-audit-log' ); ?>
				</button>
			</div>
		</form>

		<p class="description">
			<em>
				<?php
				// Step help text.
				$step_help = __( 'The plugin stores the data in the WordPress database in a very efficient way, though the more data you keep the more hard disk space it will consume. If you need need to retain a lot of data we would recommend you to <a href="https://www.wpsecurityauditlog.com/premium-features/?utm_source=plugin&utm_medium=referral&utm_campaign=WSAL&utm_content=wizard+configuration" target="_blank">upgrade to Premium</a> and use the Database tools to store the WordPress activity log in an external database.', 'wp-security-audit-log' );

				if ( wsal_freemius()->is__premium_only() ) {
					// Change the help text if premium version of the plugin is active.
					$step_help = __( 'The plugin stores the data in the WordPress database in a very efficient way, though the more data you keep the more hard disk space it will consume. If you need need to retain a lot of data we would recommend you to store the WordPress activity log in an external database or enable archiving.', 'wp-security-audit-log' );
				}
				echo wp_kses( $step_help, $this->wsal->allowed_html_tags );
				?>
			</em>
		</p>
		<?php
	}

	/**
	 * Step Save: `Log Retention`
	 */
	private function wsal_step_log_retention_save() {
		// Verify nonce.
		check_admin_referer( 'wsal-step-log-retention' );

		// Save Log Retention Step setting.
		// @codingStandardsIgnoreStart
		$pruning_limit = isset( $_POST['wsal-pruning-limit'] ) ? sanitize_text_field( $_POST['wsal-pruning-limit'] ) : false;
		// @codingStandardsIgnoreEnd

		// Save log retention setting.
		if ( ! empty( $pruning_limit ) ) {
			switch ( $pruning_limit ) {
				case '6':
					// 6 months.
					$this->wsal->SetGlobalOption( 'pruning-date-e', true );
					$this->wsal->SetGlobalOption( 'pruning-date', $pruning_limit . ' months' );
					break;

				case '12':
					// 12 months.
					$this->wsal->SetGlobalOption( 'pruning-date-e', true );
					$this->wsal->SetGlobalOption( 'pruning-date', $pruning_limit . ' months' );
					break;

				case 'none':
					// None.
					$this->wsal->SetGlobalOption( 'pruning-date-e', false );
					break;

				default:
					break;
			}
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step() ) );
		exit();
	}

	/**
	 * Get Token Type.
	 *
	 * @param string $token - Token type.
	 */
	protected function get_token_type( $token ) {
		return $this->wsal->settings->get_token_type( $token );
	}

	/**
	 * Step View: `Access`
	 */
	private function wsal_step_access() {
		?>
		<form method="post" class="wsal-setup-form">
			<?php wp_nonce_field( 'wsal-step-access' ); ?>
			<h4>
				<?php esc_html_e( 'By default only the users with administrator role can access the WordPress activity log. Would you like to allow any other user or users with a role to access the WordPress activity log?', 'wp-security-audit-log' ); ?>
			</h4>
			<fieldset>
				<label for="no">
					<input id="no" name="wsal-access" type="radio" value="no" checked />
					<?php esc_html_e( 'No', 'wp-security-audit-log' ); ?>
				</label>
				<br />
				<label for="yes">
					<input id="yes" name="wsal-access" type="radio" value="yes" />
					<?php esc_html_e( 'Yes', 'wp-security-audit-log' ); ?>
				</label>
			</fieldset>

			<fieldset>
				<label for="editor-users-box">
					<span><?php esc_html_e( 'Usernames: ', 'wp-security-audit-log' ); ?></span>
					<input id="editor-users-box" class="editor-query-box" name="editor-users-box" type="text" />
					<a href="javascript:;" class="button button-primary" id="editor-users-add">
						<?php esc_html_e( 'ADD', 'wp-security-audit-log' ); ?>
					</a>
				</label>
				<br />
				<label for="editor-roles-box">
					<span><?php esc_html_e( 'Roles: ', 'wp-security-audit-log' ); ?></span>
					<input id="editor-roles-box" class="editor-query-box" name="editor-roles-box" type="text" />
					<a href="javascript:;" class="button button-primary" id="editor-roles-add">
						<?php esc_html_e( 'ADD', 'wp-security-audit-log' ); ?>
					</a>
				</label>
				<br />
				<div id="editor-list">
					<?php foreach ( $this->wsal->settings->GetAllowedPluginEditors() as $item ) : ?>
						<span class="sectoken-<?php echo esc_attr( $this->get_token_type( $item ) ); ?>">
							<input type="hidden" name="editors[]" value="<?php echo esc_attr( $item ); ?>"/>
							<?php echo esc_html( $item ); ?>
							<?php if ( wp_get_current_user()->user_login !== $item ) { ?>
								<a href="javascript:;" title="Remove">&times;</a>
							<?php } ?>
						</span>
					<?php endforeach; ?>
				</div>
			</fieldset>

			<p class="description">
				<?php esc_html_e( 'Note: you can change the WordPress activity log privileges settings at any time from the plugin settings.', 'wp-security-audit-log' ); ?>
			</p>

			<div class="wsal-setup-actions">
				<button class="button button-primary"
					type="submit"
					name="save_step"
					value="<?php esc_attr_e( 'Next', 'wp-security-audit-log' ); ?>">
					<?php esc_html_e( 'Next', 'wp-security-audit-log' ); ?>
				</button>
			</div>
		</form>

		<p class="description">
			<em><?php echo esc_html__( 'The WordPress activity log contains sensitive data such as who logged in, from where, when, and what they did.', 'wp-security-audit-log' ); ?></em>
		</p>
		<?php
	}

	/**
	 * Step Save: `Access`
	 */
	private function wsal_step_access_save() {
		// Verify nonce.
		check_admin_referer( 'wsal-step-access' );

		// Get Access Step setting.
		// @codingStandardsIgnoreStart
		$wsal_access  = isset( $_POST['wsal-access'] ) ? sanitize_text_field( $_POST['wsal-access'] ) : false;
		$wsal_editors = isset( $_POST['editors'] ) ? array_map( 'sanitize_text_field', $_POST['editors'] ) : false;
		// @codingStandardsIgnoreEnd

		if ( ! empty( $wsal_access ) && 'yes' === $wsal_access ) {
			if ( 1 === count( $wsal_editors ) && $this->wsal->settings->IsRestrictAdmins() ) {
				$this->wsal->settings->set_restrict_plugin_setting( 'only_me' );
			} else {
				$this->wsal->settings->set_restrict_plugin_setting( 'only_selected_users' );
			}
			$this->wsal->settings->SetAllowedPluginEditors( ! empty( $wsal_editors ) ? $wsal_editors : array() );
		} elseif ( ! empty( $wsal_access ) && 'no' === $wsal_access ) {
			if ( $this->wsal->settings->IsRestrictAdmins() ) {
				$this->wsal->settings->set_restrict_plugin_setting( 'only_me' );
			} else {
				$this->wsal->settings->SetAllowedPluginEditors( array() );
			}
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step() ) );
		exit();
	}

	/**
	 * Step View: `Exclude Objects`
	 */
	private function wsal_step_exclude_object() {
		?>
		<form method="post" class="wsal-setup-form">
			<?php wp_nonce_field( 'wsal-step-exclude-objects' ); ?>
			<p>
				<?php esc_html_e( 'The plugin will keep a log of everything that happens on your WordPress website. If you would like to exclude a particular user, users with a role or an IP address from the log specify them below. If not just click the Next button.', 'wp-security-audit-log' ); ?>
			</p>

			<fieldset>
				<label for="exuser-query-box">
					<span><?php esc_html_e( 'Usernames: ', 'wp-security-audit-log' ); ?></span>
					<input id="exuser-query-box" class="exuser-query-box" name="exuser-query-box" type="text" />
					<a href="javascript:;" class="button button-primary" id="exuser-query-add">
						<?php esc_html_e( 'ADD', 'wp-security-audit-log' ); ?>
					</a>
				</label>
				<div id="exuser-list">
					<?php foreach ( $this->wsal->settings->GetExcludedMonitoringUsers() as $item ) : ?>
						<span class="sectoken-<?php echo esc_attr( $this->get_token_type( $item ) ); ?>">
						<input type="hidden" name="exusers[]" value="<?php echo esc_attr( $item ); ?>"/>
						<?php echo esc_html( $item ); ?>
						<a href="javascript:;" title="Remove">&times;</a>
						</span>
					<?php endforeach; ?>
				</div>
			</fieldset>

			<fieldset>
				<label for="exrole-query-box">
					<span><?php esc_html_e( 'Roles: ', 'wp-security-audit-log' ); ?></span>
					<input id="exrole-query-box" class="exrole-query-box" name="exrole-query-box" type="text" />
					<a href="javascript:;" class="button button-primary" id="exrole-query-add">
						<?php esc_html_e( 'ADD', 'wp-security-audit-log' ); ?>
					</a>
				</label>
				<div id="exrole-list">
					<?php foreach ( $this->wsal->settings->GetExcludedMonitoringRoles() as $item ) : ?>
						<span class="sectoken-<?php echo esc_attr( $this->get_token_type( $item ) ); ?>">
						<input type="hidden" name="exroles[]" value="<?php echo esc_attr( $item ); ?>"/>
						<?php echo esc_html( $item ); ?>
						<a href="javascript:;" title="Remove">&times;</a>
						</span>
					<?php endforeach; ?>
				</div>
			</fieldset>

			<fieldset>
				<label for="ipaddr-query-box">
					<span><?php esc_html_e( 'IP Address: ', 'wp-security-audit-log' ); ?></span>
					<input id="ipaddr-query-box" class="ipaddr-query-box" name="ipaddr-query-box" type="text" />
					<a href="javascript:;" class="button button-primary" id="ipaddr-query-add">
						<?php esc_html_e( 'ADD', 'wp-security-audit-log' ); ?>
					</a>
				</label>
				<div id="ipaddr-list">
					<?php foreach ( $this->wsal->settings->GetExcludedMonitoringIP() as $item ) : ?>
						<span class="sectoken-<?php echo esc_attr( $this->get_token_type( $item ) ); ?>">
							<input type="hidden" name="ipaddrs[]" value="<?php echo esc_attr( $item ); ?>"/>
							<?php echo esc_html( $item ); ?>
							<a href="javascript:;" title="Remove">&times;</a>
						</span>
					<?php endforeach; ?>
				</div>
			</fieldset>

			<p class="description">
				<?php esc_html_e( 'Note: You can change these exclusions anytime from the plugin settings.', 'wp-security-audit-log' ); ?>
			</p>

			<div class="wsal-setup-actions">
				<button class="button button-primary"
					type="submit"
					name="save_step"
					value="<?php esc_attr_e( 'Next', 'wp-security-audit-log' ); ?>">
					<?php esc_html_e( 'Next', 'wp-security-audit-log' ); ?>
				</button>
			</div>
		</form>

		<p class="description">
			<em><?php echo esc_html__( 'The WordPress activity log contains sensitive data such as who logged in, from where, when and what they did.', 'wp-security-audit-log' ); ?></em>
		</p>
		<?php
	}

	/**
	 * Step Save: `Exclude Objects`
	 */
	private function wsal_step_exclude_object_save() {
		// Verify nonce.
		check_admin_referer( 'wsal-step-exclude-objects' );

		// Get exclude objects step settings.
		// @codingStandardsIgnoreStart
		$wsal_exusers = isset( $_POST['exusers'] ) ? array_map( 'sanitize_text_field', $_POST['exusers'] ) : false;
		$wsal_exroles = isset( $_POST['exroles'] ) ? array_map( 'sanitize_text_field', $_POST['exroles'] ) : false;
		$wsal_ipaddrs = isset( $_POST['ipaddrs'] ) ? array_map( 'sanitize_text_field', $_POST['ipaddrs'] ) : false;
		// @codingStandardsIgnoreEnd

		// Save the settings.
		$this->wsal->settings->SetExcludedMonitoringUsers( ! empty( $wsal_exusers ) ? $wsal_exusers : array() );
		$this->wsal->settings->SetExcludedMonitoringRoles( ! empty( $wsal_exroles ) ? $wsal_exroles : array() );
		$this->wsal->settings->SetExcludedMonitoringIP( ! empty( $wsal_ipaddrs ) ? $wsal_ipaddrs : array() );

		wp_safe_redirect( esc_url_raw( $this->get_next_step() ) );
		exit();
	}

	/**
	 * Step View: `Finish`
	 */
	private function wsal_step_finish() {
		?>
		<p><?php esc_html_e( 'Your plugin is all set and it is ready to start keeping a record of everything that is happening on your WordPress in a WordPress activity log.', 'wp-security-audit-log' ); ?></p>
		<p><?php esc_html_e( 'Below are a few useful links you might need to refer to:', 'wp-security-audit-log' ); ?></p>

		<ul>
			<li>
				<a href="https://www.wpsecurityauditlog.com/support-documentation/getting-started-wp-security-audit-log/?utm_source=plugin&utm_medium=referral&utm_campaign=WSAL&utm_content=wizard+configuration" target="_blank">
					<?php esc_html_e( 'Getting started with the WP Security Audit Log plugin', 'wp-security-audit-log' ); ?>
				</a>
			</li>
			<li>
				<a href="https://www.wpsecurityauditlog.com/support-documentation/?utm_source=plugin&utm_medium=referral&utm_campaign=WSAL&utm_content=wizard+configuration" target="_blank">
					<?php esc_html_e( 'Knowledge Base & Support Documents', 'wp-security-audit-log' ); ?>
				</a>
			</li>
			<li>
				<a href="https://www.wpsecurityauditlog.com/benefits-wordpress-activity-log/?utm_source=plugin&utm_medium=referral&utm_campaign=WSAL&utm_content=wizard+configuration" target="_blank">
					<?php esc_html_e( 'Benefits of keeping a WordPress activity log', 'wp-security-audit-log' ); ?>
				</a>
			</li>
		</ul>

		<p><?php echo wp_kses( __( 'We trust this plugin meets all your activity log requirements. Should you encounter any problems, have feature requests or would like to share some feedback, <a href="https://www.wpsecurityauditlog.com/contact/?utm_source=plugin&utm_medium=referral&utm_campaign=WSAL&utm_content=wizard+configuration" target="_blank">please get in touch!</a>', 'wp-security-audit-log' ), $this->wsal->allowed_html_tags ); ?></p>

		<form method="post" class="wsal-setup-form">
			<?php wp_nonce_field( 'wsal-step-finish' ); ?>
			<div class="wsal-setup-actions">
				<button class="button button-primary"
					type="submit"
					name="save_step"
					value="<?php esc_attr_e( 'Finish', 'wp-security-audit-log' ); ?>">
					<?php esc_html_e( 'Finish', 'wp-security-audit-log' ); ?>
				</button>
			</div>
		</form>
		<?php
	}

	/**
	 * Step Save: `Finish`
	 */
	private function wsal_step_finish_save() {
		// Verify nonce.
		check_admin_referer( 'wsal-step-finish' );

		// Mark the finish of the setup.
		$this->wsal->SetGlobalOption( 'wsal-setup-complete', 'yes' );

		wp_safe_redirect( esc_url_raw( $this->get_next_step() ) );
		exit();
	}
}
