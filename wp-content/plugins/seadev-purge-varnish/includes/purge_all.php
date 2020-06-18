<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$msg = '';
$purge_varnish = new Purge_Varnish();

if (isset($_POST['purge_all']) && $_POST['purge_all'] == 'Purge all') {
  if($purge_varnish->purge_varnish_nonce('purgeAllCache') == true) {
     $msg = $purge_varnish->purge_varnish_all_cache_manually();
  }
}
?>
<div class="purge_varnish">
  <div class="screen">
    <h2><?php print esc_html_e($title); ?></h2>
    <form action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post" style="text-align:center">
      <p><?php print $msg; ?></p>
      <p><?php esc_html_e('To clear whole site varnish cache, Click on Purge all button') ?></p>
      <p><?php wp_nonce_field('purgeAllCache');?></p>
      <p><input type="submit" value="Purge all" name="purge_all" /></p>
    </form>
  </div>
</div>