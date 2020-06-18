<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'seadev_container_type' );
$header_toggle = get_theme_mod( 'seadev_header_toggle' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<meta name="msvalidate.01" content="33D1011FD54CD9D32CE0A53F8826A019" />
	<?php wp_head(); ?>
</head>

<?php do_action('seadev_before_body_open_tag'); ?>

<body <?php body_class(); ?>>

<?php do_action('seadev_after_body_open_tag'); ?>

<div class="site" id="page">
	

	<!-- ******************* The Navbar Area ******************* -->
	<div id="wrapper-navbar" itemscope itemtype="http://schema.org/WebSite">

		<a class="skip-link sr-only sr-only-focusable" href="#content"><?php esc_html_e( 'Skip to content', 'seadev' ); ?></a>

		<nav class="navbar navbar-dark bg-primary <?php echo $header_toggle; ?>">

		<?php if ( 'container' == $container ) : ?>
			<div class="container">
		<?php endif; ?>
				<div class="navbar-header">
					<!-- Your site title as branding in the menu -->
					<?php if ( ! has_custom_logo() ) { ?>

					<?php if ( is_front_page() && is_home() ) : ?>

						<h1 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>

					<?php else : ?>

						<a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a>

					<?php endif; ?>


					<?php } else {
					the_custom_logo();
					} ?><!-- end custom logo -->

					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'seadev' ); ?>">
					<span class="navbar-toggler-icon"></span>
					</button>
				</div>
									
				<div class="navbar-right">
					<div class="header-topbar">
						<!-- social media	 -->
						<div class="chanel-social">
							<?php
							// Use shortcodes in form like Landing Page Template.
								echo do_shortcode('[seadev-social-media]');
							?>
						</div>
						<!-- search-form -->
						<div class="search-form">
							<?php
							// Use shortcodes in form like Landing Page Template.
								echo get_search_form();
							?>
						</div>
					</div>
					<div>
						<!-- The WordPress Menu goes here -->
						<?php wp_nav_menu(
						array(
							'theme_location'  => 'primary',
							'container_class' => 'collapse navbar-collapse',
							'container_id'    => 'navbarNavDropdown',
							'menu_class'      => 'navbar-nav ml-auto',
							'fallback_cb'     => '',
							'menu_id'         => 'main-menu',
							'depth'           => 2,
							'walker'          => new Seadev_WP_Bootstrap_Navwalker(),
							)
						); ?>
					</div>
					
				</div>
				
			<?php if ( 'container' == $container ) : ?>
			</div><!-- .container -->
			<?php endif; ?>

		</nav><!-- .site-navigation -->

	</div><!-- #wrapper-navbar end --