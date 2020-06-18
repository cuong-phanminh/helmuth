<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! is_active_sidebar( 'right-sidebar' ) ) {
	return;
}
?>

<div class="col-md-4 widget-area" id="secondary" role="complementary">

	<?php //dynamic_sidebar( 'right-sidebar' ); ?>

</div><!-- #secondary -->
