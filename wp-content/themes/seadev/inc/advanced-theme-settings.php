<?php
/**
 * Get advanced theme settings
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get a specific setting from ACF options page
function seadev_get_theme_setting( $setting ) {
  return get_field($setting, 'option');
}

// Get API keys setting
function seadev_get_api_key( $name ) {

  $seadev_api_keys = seadev_get_theme_setting('seadev_api_keys');

  $api = array();
  if ( count($seadev_api_keys) > 0 ) {
    foreach ($seadev_api_keys as $value) {
      $api_name = $value['seadev_api_key_name'];
      $api_value = $value['seadev_api_key_value'];
      $api[$api_name] = $api_value;
    }
  }

  if ( array_key_exists($name, $api) ) {
    return $api[$name];
  } else {
    return null;
  }
}

// Add Google Analytics to head tag
function seadev_add_google_analytics_tracking_code(){

  $use_gtm = seadev_get_theme_setting('seadev_use_gtm');
	$ga_id = seadev_get_theme_setting('seadev_ga_tracking_id');

	if (!$use_gtm && $ga_id != "") : ?>

		<!-- Google Analytics -->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', '<?php echo $ga_id; ?>', 'auto');
      ga('send', 'pageview');
    </script>
    <!-- End Google Analytics -->

  <?php endif;
}
add_action('wp_head', 'seadev_add_google_analytics_tracking_code', 9999);

// Add Facebook Pixel code to head tag
function seadev_add_facebook_pixel_code(){

  $use_gtm = seadev_get_theme_setting('seadev_use_gtm');
	$fb_pixel_id = seadev_get_theme_setting('seadev_fb_pixel_id');

	if (!$use_gtm && $fb_pixel_id != "") : ?>

		<!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?php echo $fb_pixel_id; ?>');
      fbq('track', 'PageView');
    </script>
    <noscript>
      <img height="1" width="1" style="display:none"
          src="https://www.facebook.com/tr?id=<?php echo $fb_pixel_id; ?>&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->

  <?php endif;
}
add_action('wp_head', 'seadev_add_facebook_pixel_code', 9999);


// Add Google Tag Manager code to head tag
function seadev_add_google_tag_manager_code_to_head(){

  $use_gtm = seadev_get_theme_setting('seadev_use_gtm');
	$gtm_id = seadev_get_theme_setting('seadev_gtm_id');

	if ($use_gtm && $gtm_id != "") : ?>

		<!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer', '<?php echo $gtm_id; ?>');</script>
    <!-- End Google Tag Manager -->

  <?php endif;
}
add_action('wp_head', 'seadev_add_google_tag_manager_code_to_head', 9999);

// Add additional Google Tag Manager code to head body
function seadev_add_google_tag_manager_code_to_body(){

  $use_gtm = seadev_get_theme_setting('seadev_use_gtm');
	$gtm_id = seadev_get_theme_setting('seadev_gtm_id');

	if ($use_gtm && $gtm_id != "") : ?>

		<!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gtm_id; ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

  <?php endif;
}
add_action('seadev_after_body_open_tag', 'seadev_add_google_tag_manager_code_to_body');

