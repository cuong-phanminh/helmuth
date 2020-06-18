<?php
/**
 * Sidebar setup for footer top.
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'seadev_container_type' );

?>

<?php if ( is_active_sidebar( 'footertop' ) ) : ?>

	<!-- ******************* The Footer Top Full-width Widget Area ******************* -->

	<div class="wrapper" id="wrapper-footer-top">

		<div class="<?php echo esc_attr( $container ); ?>" id="footer-top-content" tabindex="-1">

			<div class="row">

				<?php dynamic_sidebar( 'footertop' ); ?>

			</div>

		</div>

	</div><!-- #wrapper-footer-top -->

<?php endif; ?>
