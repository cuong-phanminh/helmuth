<?php

/**
 * Seadev Theme Shortcodes
 *
 * @package seadev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


function seadev_social_media() {
	ob_start();
?>
	<?php if ( have_rows( 'seadev_social_media', 'option' ) ) : ?>
	<ul class="seadev-social-media-list list-inline">
		<?php while ( have_rows( 'seadev_social_media', 'option' ) ) : the_row(); ?>
			<?php
				$channel = get_sub_field( 'seadev_social_media_channel' );
				$icon = get_sub_field( 'seadev_social_media_fa_icon' );
				$url = get_sub_field( 'seadev_social_media_url' );
			?>
			<li class="seadev-social-media-item list-inline-item">
				<a target="_blank" href="<?php echo $url; ?>" class="channel-<?php echo $channel; ?>">
				<?php if ($icon != "") : ?>
					<i class="fa <?php echo $icon; ?>"></i>
				<?php else: ?>
					<i class="fa fa-<?php echo $channel; ?>"></i>
				<?php endif; ?>
				</a>
			</li>
		<?php endwhile; ?>
	</ul>	
<?php endif; ?>

<?php

	return ob_get_clean();
}
add_shortcode('seadev-social-media', 'seadev_social_media');

