<?php
/**
 * Sidebar setup for footer bottom.
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'seadev_container_type' );

?>

<?php if ( is_active_sidebar( 'footerbottom' ) ) : ?>

	<!-- ******************* The Footer Bottom Full-width Widget Area ******************* -->

	<div class="wrapper" id="wrapper-footer-bottom">

		<div class="<?php echo esc_attr( $container ); ?>" id="footer-bottom-content" tabindex="-1">

			<div class="row">

				<?php dynamic_sidebar( 'footerbottom' ); ?>

			</div>

		</div>

	</div><!-- #wrapper-footer-bottom -->

<?php endif; ?>
