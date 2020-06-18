<?php
defined( 'ABSPATH' ) || die();

$tab = ( ! empty( $_GET['tab'] ) ) ? esc_attr( $_GET['tab'] ) : 'captcha';

$tabs = array(
	'captcha'        => esc_html__( 'Captcha', 'login-security-recaptcha' ),
	'error_logs'     => esc_html__( 'Error Logs', 'login-security-recaptcha' ),
	'reset'          => esc_html__( 'Reset', 'login-security-recaptcha' ),
);
?>
<div class="wrap stlsr">
	<h1><?php esc_html_e( 'Login Security Recaptcha', 'login-security-recaptcha' ); ?></h1>
	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $tabs as $key => $value ) {
			$class = ( $tab === $key ) ? ' nav-tab-active' : '';
		?>
		<a class="nav-tab<?php echo esc_attr( $class ); ?>" href="?page=stlsr_settings&tab=<?php echo esc_attr( $key ); ?>">
			<?php echo esc_html( $value ); ?>
		</a>
		<?php
		}
		?>
	</h2>
	<div class="stlsr-section">
	<?php
	if ( 'captcha' === $tab ) {
		require_once STLSR_PLUGIN_DIR_PATH . 'admin/inc/setting/tabs/captcha.php';
	} elseif ( 'error_logs' === $tab ) {
		require_once STLSR_PLUGIN_DIR_PATH . 'admin/inc/setting/tabs/error-logs.php';
	} elseif ( 'reset' === $tab ) {
		require_once STLSR_PLUGIN_DIR_PATH . 'admin/inc/setting/tabs/reset.php';
	}
	?>
	</div>
</div>
