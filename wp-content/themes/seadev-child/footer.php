<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'seadev_container_type' );
?>

<div id="wrapper-footer">

	<footer class="site-footer" id="footer">

		<?php get_template_part( 'sidebar-templates/sidebar', 'footertop' ); ?>

		<?php get_template_part( 'sidebar-templates/sidebar', 'footerbottom' ); ?>

	</footer>

</div>

<?php wp_footer(); ?>

</body>

</html>

