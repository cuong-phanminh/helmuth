<?php
defined( 'ABSPATH' ) || die();
require_once STLSR_PLUGIN_DIR_PATH . 'includes/class-stlsr-helper.php';

$captcha_list = STLSR_Helper::captcha_list();

$steps_url = STLSR_Helper::get_steps_url();

$google_recaptcha_v2_themes = STLSR_Helper::google_recaptcha_v2_themes();

$google_recaptcha_v3_scores = STLSR_Helper::google_recaptcha_v3_scores();

$google_recaptcha_v2 = get_option( 'stlsr_google_recaptcha_v2' );

$google_recaptcha_v2      = get_option( 'stlsr_google_recaptcha_v2' );
$grecaptcha_v2_site_key   = isset( $google_recaptcha_v2['site_key'] ) ? esc_attr( $google_recaptcha_v2['site_key'] ) : '';
$grecaptcha_v2_secret_key = isset( $google_recaptcha_v2['secret_key'] ) ? esc_attr( $google_recaptcha_v2['secret_key'] ) : '';
$grecaptcha_v2_theme      = isset( $google_recaptcha_v2['theme'] ) ? esc_attr( $google_recaptcha_v2['theme'] ) : 'light';

$google_recaptcha_v3      = get_option( 'stlsr_google_recaptcha_v3' );
$grecaptcha_v3_site_key   = isset( $google_recaptcha_v3['site_key'] ) ? esc_attr( $google_recaptcha_v3['site_key'] ) : '';
$grecaptcha_v3_secret_key = isset( $google_recaptcha_v3['secret_key'] ) ? esc_attr( $google_recaptcha_v3['secret_key'] ) : '';
$grecaptcha_v3_score      = isset( $google_recaptcha_v3['score'] ) ? esc_attr( $google_recaptcha_v3['score'] ) : '0.3';

$login_captcha        = get_option( 'stlsr_login_captcha' );
$login_captcha_enable = isset( $login_captcha['enable'] ) ? (bool) $login_captcha['enable'] : false;
$login_captcha        = isset( $login_captcha['captcha'] ) ? esc_attr( $login_captcha['captcha'] ) : '';

$lostpassword_captcha        = get_option( 'stlsr_lostpassword_captcha' );
$lostpassword_captcha_enable = isset( $lostpassword_captcha['enable'] ) ? (bool) $lostpassword_captcha['enable'] : false;
$lostpassword_captcha        = isset( $lostpassword_captcha['captcha'] ) ? esc_attr( $lostpassword_captcha['captcha'] ) : '';

$register_captcha        = get_option( 'stlsr_register_captcha' );
$register_captcha_enable = isset( $register_captcha['enable'] ) ? (bool) $register_captcha['enable'] : false;
$register_captcha        = isset( $register_captcha['captcha'] ) ? esc_attr( $register_captcha['captcha'] ) : '';

$comment_captcha        = get_option( 'stlsr_comment_captcha' );
$comment_captcha_enable = isset( $comment_captcha['enable'] ) ? (bool) $comment_captcha['enable'] : false;
$comment_captcha        = isset( $comment_captcha['captcha'] ) ? esc_attr( $comment_captcha['captcha'] ) : '';

$google_recaptcha_v2_enable = false;
$google_recaptcha_v3_enable = false;

if ( $login_captcha_enable ) {
	if ( 'google_recaptcha_v2' === $login_captcha ) {
		$google_recaptcha_v2_enable = true;
	}
	if ( 'google_recaptcha_v3' === $login_captcha ) {
		$google_recaptcha_v3_enable = true;
	}
}

if ( $lostpassword_captcha_enable ) {
	if ( ! $google_recaptcha_v2_enable && ( 'google_recaptcha_v2' === $lostpassword_captcha ) ) {
		$google_recaptcha_v2_enable = true;
	}
	if ( ! $google_recaptcha_v3_enable && ( 'google_recaptcha_v3' === $lostpassword_captcha ) ) {
		$google_recaptcha_v3_enable = true;
	}
}

if ( $register_captcha_enable ) {
	if ( ! $google_recaptcha_v2_enable && ( 'google_recaptcha_v2' === $register_captcha ) ) {
		$google_recaptcha_v2_enable = true;
	}
	if ( ! $google_recaptcha_v3_enable && ( 'google_recaptcha_v3' === $register_captcha ) ) {
		$google_recaptcha_v3_enable = true;
	}
}

if ( $comment_captcha_enable ) {
	if ( ! $google_recaptcha_v2_enable && ( 'google_recaptcha_v2' === $comment_captcha ) ) {
		$google_recaptcha_v2_enable = true;
	}
	if ( ! $google_recaptcha_v3_enable && ( 'google_recaptcha_v3' === $comment_captcha ) ) {
		$google_recaptcha_v3_enable = true;
	}
}

if ( $google_recaptcha_v2_enable ) {
	if ( empty( $grecaptcha_v2_site_key ) ) {
	?>
	<div class="st-alert-box notice notice-warning is-dismissible">
		<p><?php esc_html_e( 'Please set the the "Site Key" for "Google reCAPTCHA Version 2".', 'login-security-recaptcha' ); ?></p>
	</div>
	<?php
	}
	if ( empty( $grecaptcha_v2_secret_key ) ) {
	?>
	<div class="st-alert-box notice notice-warning is-dismissible">
		<p><?php esc_html_e( 'Please set the the "Secret Key" for "Google reCAPTCHA Version 2".', 'login-security-recaptcha' ); ?></p>
	</div>
	<?php
	}
}

if ( $google_recaptcha_v3_enable ) {
	if ( empty( $grecaptcha_v3_site_key ) ) {
	?>
	<div class="st-alert-box notice notice-warning is-dismissible">
		<p><?php esc_html_e( 'Please set the the "Site Key" for "Google reCAPTCHA Version 3".', 'login-security-recaptcha' ); ?></p>
	</div>
	<?php
	}
	if ( empty( $grecaptcha_v3_secret_key ) ) {
	?>
	<div class="st-alert-box notice notice-warning is-dismissible">
		<p><?php esc_html_e( 'Please set the the "Secret Key" for "Google reCAPTCHA Version 3".', 'login-security-recaptcha' ); ?></p>
	</div>
	<?php
	}
}
?>

<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="stlsr-save-captcha-form">

	<?php $nonce = wp_create_nonce( 'save-captcha' ); ?>
	<input type="hidden" name="save-captcha" value="<?php echo esc_attr( $nonce ); ?>">

	<input type="hidden" name="action" value="stlsr-save-captcha">

	<table class="form-table">
		<tbody>

			<tr>
				<th scope="row"><?php esc_html_e( 'Enable Captcha', 'login-security-recaptcha' ); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Enable Captcha at Login Form', 'login-security-recaptcha' ); ?></span>
						</legend>
						<label for="stlsr_login_captcha_enable">
							<input <?php checked( $login_captcha_enable, true, true ); ?> name="login_captcha_enable" type="checkbox" id="stlsr_login_captcha_enable" value="1">
							<?php esc_html_e( 'Enable at Login Form', 'login-security-recaptcha' ); ?>
						</label>
					</fieldset>
					<div class="stlsr_login_captcha">
						<select name="login_captcha">
							<?php foreach ( $captcha_list as $key => $value ) { ?>
							<option <?php selected( $login_captcha, $key, true ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
							<?php } ?>
						</select>
						<hr>
					</div>

					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Enable Captcha at Lost Password Form', 'login-security-recaptcha' ); ?></span>
						</legend>
						<label for="stlsr_lostpassword_captcha_enable">
							<input <?php checked( $lostpassword_captcha_enable, true, true ); ?> name="lostpassword_captcha_enable" type="checkbox" id="stlsr_lostpassword_captcha_enable" value="1">
							<?php esc_html_e( 'Enable at Lost Password Form', 'login-security-recaptcha' ); ?>
						</label>
					</fieldset>
					<div class="stlsr_lostpassword_captcha">
						<select name="lostpassword_captcha">
							<?php foreach ( $captcha_list as $key => $value ) { ?>
							<option <?php selected( $lostpassword_captcha, $key, true ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
							<?php } ?>
						</select>
						<hr>
					</div>

					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Enable Captcha at Registration Form', 'login-security-recaptcha' ); ?></span>
						</legend>
						<label for="stlsr_register_captcha_enable">
							<input <?php checked( $register_captcha_enable, true, true ); ?> name="register_captcha_enable" type="checkbox" id="stlsr_register_captcha_enable" value="1">
							<?php esc_html_e( 'Enable at Registration Form', 'login-security-recaptcha' ); ?>
						</label>
					</fieldset>
					<div class="stlsr_register_captcha">
						<select name="register_captcha" class="stlsr_register_captcha">
							<?php foreach ( $captcha_list as $key => $value ) { ?>
							<option <?php selected( $register_captcha, $key, true ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
							<?php } ?>
						</select>
						<hr>
					</div>

					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Enable Captcha at Comment Form', 'login-security-recaptcha' ); ?></span>
						</legend>
						<label for="stlsr_comment_captcha_enable">
							<input <?php checked( $comment_captcha_enable, true, true ); ?> name="comment_captcha_enable" type="checkbox" id="stlsr_comment_captcha_enable" value="1">
							<?php esc_html_e( 'Enable at Comment Form', 'login-security-recaptcha' ); ?>
						</label>
					</fieldset>
					<div class="stlsr_comment_captcha">
						<p class="description"><?php esc_html_e( 'Only when user is not logged in.', 'login-security-recaptcha' ); ?></p>
						<select name="comment_captcha">
							<?php foreach ( $captcha_list as $key => $value ) { ?>
							<option <?php selected( $comment_captcha, $key, true ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
							<?php } ?>
						</select>
						<hr>
					</div>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Captcha Settings', 'login-security-recaptcha' ); ?></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Set Captcha Settings', 'login-security-recaptcha' ); ?></span>
						</legend>
						<?php
						foreach ( $captcha_list as $key => $value ) {
						?>
						<label>
							<?php reset( $captcha_list ); ?>
							<input <?php checked( $key === key( $captcha_list ), true, true ); ?> type="radio" name="captcha" value="<?php echo esc_attr( $key ); ?>">
							<span><?php echo esc_html( $value ); ?></span>
						</label>
							<?php
							end( $captcha_list );
							if ( $key !== key( $captcha_list ) ) {
								echo '<br>';
							}
						}
						?>
					</fieldset>
				</td>
			</tr>

			<tr class="stlsr_captcha stlsr_google_recaptcha_v2">
				<th scope="row">
					<label for="stlsr_google_recaptcha_v2_site_key"><?php esc_html_e( 'Site Key', 'login-security-recaptcha' ); ?></label>
				</th>
				<td>
					<input name="google_recaptcha_v2_site_key" type="text" id="stlsr_google_recaptcha_v2_site_key" value="<?php echo esc_attr( $grecaptcha_v2_site_key ); ?>" class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Enter Google reCAPTCHA v2 Site Key.', 'login-security-recaptcha' ); ?>&nbsp;
						<a target="_blank" href="<?php echo esc_url( $steps_url ); ?>"><?php esc_html_e( 'Click Here', 'login-security-recaptcha' ); ?></a>
					</p>
				</td>
			</tr>

			<tr class="stlsr_captcha stlsr_google_recaptcha_v2">
				<th scope="row">
					<label for="stlsr_google_recaptcha_v2_secret_key"><?php esc_html_e( 'Secret Key', 'login-security-recaptcha' ); ?></label>
				</th>
				<td>
					<input name="google_recaptcha_v2_secret_key" type="text" id="stlsr_google_recaptcha_v2_secret_key" value="<?php echo esc_attr( $grecaptcha_v2_secret_key ); ?>" class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Enter Google reCAPTCHA v2 Secret Key.', 'login-security-recaptcha' ); ?>&nbsp;
						<a target="_blank" href="<?php echo esc_url( $steps_url ); ?>"><?php esc_html_e( 'Click Here', 'login-security-recaptcha' ); ?></a>
					</p>
				</td>
			</tr>

			<tr class="stlsr_captcha stlsr_google_recaptcha_v2">
				<th scope="row">
					<label for="stlsr_google_recaptcha_v2_theme"><?php esc_html_e( 'Theme', 'login-security-recaptcha' ); ?></label>
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Select Google reCAPTCHA Version 2 Theme.', 'login-security-recaptcha' ); ?></span>
						</legend>
						<?php
						foreach ( $google_recaptcha_v2_themes as $key => $value ) {
						?>
						<label>
							<input <?php checked( $grecaptcha_v2_theme, $key, true ); ?> type="radio" name="google_recaptcha_v2_theme" value="<?php echo esc_attr( $key ); ?>">
							<span><?php echo esc_html( $value ); ?></span>
						</label>
							<?php
							end( $google_recaptcha_v2_themes );
							if ( $key !== key( $google_recaptcha_v2_themes ) ) {
								echo '<br>';
							}
						}
						?>
					</fieldset>
					<p class="description"><?php esc_html_e( 'Select Google reCAPTCHA Version 2 Theme.', 'login-security-recaptcha' ); ?></p>
				</td>
			</tr>

			<tr class="stlsr_captcha stlsr_google_recaptcha_v3">
				<th scope="row">
					<label for="stlsr_google_recaptcha_v3_site_key"><?php esc_html_e( 'Site Key', 'login-security-recaptcha' ); ?></label>
				</th>
				<td>
					<input name="google_recaptcha_v3_site_key" type="text" id="stlsr_google_recaptcha_v3_site_key" value="<?php echo esc_attr( $grecaptcha_v3_site_key ); ?>" class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Enter Google reCAPTCHA v3 Site Key.', 'login-security-recaptcha' ); ?>&nbsp;
						<a target="_blank" href="<?php echo esc_url( $steps_url ); ?>"><?php esc_html_e( 'Click Here', 'login-security-recaptcha' ); ?></a>
					</p>
				</td>
			</tr>

			<tr class="stlsr_captcha stlsr_google_recaptcha_v3">
				<th scope="row">
					<label for="stlsr_google_recaptcha_v3_secret_key"><?php esc_html_e( 'Secret Key', 'login-security-recaptcha' ); ?></label>
				</th>
				<td>
					<input name="google_recaptcha_v3_secret_key" type="text" id="stlsr_google_recaptcha_v3_secret_key" value="<?php echo esc_attr( $grecaptcha_v3_secret_key ); ?>" class="regular-text">
					<p class="description">
						<?php esc_html_e( 'Enter Google reCAPTCHA v3 Secret Key.', 'login-security-recaptcha' ); ?>&nbsp;
						<a target="_blank" href="<?php echo esc_url( $steps_url ); ?>"><?php esc_html_e( 'Click Here', 'login-security-recaptcha' ); ?></a>
					</p>
				</td>
			</tr>

			<tr class="stlsr_captcha stlsr_google_recaptcha_v3">
				<th scope="row">
					<label for="stlsr_google_recaptcha_v3_score"><?php esc_html_e( 'Score', 'login-security-recaptcha' ); ?></label>
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php esc_html_e( 'Select Google reCAPTCHA Version 3 Score.', 'login-security-recaptcha' ); ?></span>
						</legend>
						<?php
						foreach ( $google_recaptcha_v3_scores as $key => $value ) {
						?>
						<label>
							<input <?php checked( $grecaptcha_v3_score, $key, true ); ?> type="radio" name="google_recaptcha_v3_score" value="<?php echo esc_attr( $key ); ?>">
							<span><?php echo esc_html( $value ); ?></span>
						</label>
							<?php
							end( $google_recaptcha_v3_scores );
							if ( $key !== key( $google_recaptcha_v3_scores ) ) {
								echo '&nbsp;&nbsp;';
							}
						}
						?>
					</fieldset>
					<p class="description"><?php esc_html_e( 'reCAPTCHA v3 returns a score (0.0 is very likely a bot). Select Google reCAPTCHA Version 3 Score.', 'login-security-recaptcha' ); ?></p>
				</td>
			</tr>

		</tbody>
	</table>

	<button type="submit" class="button button-primary" id="stlsr-save-captcha-btn"><?php esc_html_e( 'Save Changes', 'login-security-recaptcha' ); ?></button>

</form>

<div class="stlsr-other">
	<div class="stlsr-review-us">
		<a target="_blank" href="https://wordpress.org/support/plugin/login-security-recaptcha/reviews/#new-post" class="stlsr-review-link">
			<span class="stlsr-rate-us">
				<?php esc_html_e( 'Like this plugin? Leave a Review.', 'login-security-recaptcha' ); ?>
			</span>
			<div class="vers column-rating">
				<div class="star-rating">
					<span class="screen-reader-text"><?php esc_html_e( 'Like this plugin? Leave a Review.', 'login-security-recaptcha' ); ?></span>
					<div class="star star-full" aria-hidden="true"></div>
					<div class="star star-full" aria-hidden="true"></div>
					<div class="star star-full" aria-hidden="true"></div>
					<div class="star star-full" aria-hidden="true"></div>
					<div class="star star-full" aria-hidden="true"></div>
				</div>
			</div>
		</a>
	</div>
</div>
