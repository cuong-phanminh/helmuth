<?php 
/**
 * Custom functions
 */

// Create section tag with id, class and inline-style
function open_section( $id, $classes = array(), $styles = array() ) {

	$class_arr = array();
	if ( ! empty( $classes ) ) {
		foreach ( $classes as $class ) {
			$class_arr[] = $class;
		}
		$extra_classes = implode( ' ', $class_arr );
	} else {
		$extra_classes = null;
	}

	$inline_style = "";
	if ( ! empty( $styles ) ) {
		foreach ( $styles as $key => $value ) {
			$inline_style .= "$key:$value;";
		}
	}

	echo "<section id='$id' class='page-section $extra_classes' style='$inline_style'>";
}

function close_section() {
	echo "</section>";
}

/*
* Add <meta name="robots" content="noindex"> to local, dev, staging environments
*/
function seadev_noindex_is_internal_site(){
	$blog_url = get_bloginfo('url');
	if(preg_match('/.*?(seadev\.cc|seadev.co|\.local).*?/is', $blog_url)){
		return true;
	} 
	return false;
}
function seadev_add_noindex_metadata(){
	echo '<meta name="robots" content="noindex">';
}
function seadev_remove_robot_metadata(){
    // If the Yoast plugin isn't installed, don't run this
    if ( ! is_callable( array( 'WPSEO_Frontend', 'get_instance' ) ) ) {
        return;
    }
	if (seadev_noindex_is_internal_site() === true) {
		// Get the Yoast instantiated class
		$yoast = WPSEO_Frontend::get_instance();
		// remove_action( 'wpseo_head', array( $yoast, 'head' ), 50 );
		// per Yoast code, this is priority 6
		// remove_action( 'wpseo_head', array( $yoast, 'metadesc' ), 6 );
		// per Yoast code, this is priority 10
		remove_action( 'wpseo_head', array( $yoast, 'robots' ), 10 );
		// per Yoast code, this is priority 11
		// remove_action( 'wpseo_head', array( $yoast, 'metakeywords' ), 11 );
		// per Yoast code, this is priority 20
		// remove_action( 'wpseo_head', array( $yoast, 'canonical' ), 20 );
		// per Yoast code, this is priority 21
		// remove_action( 'wpseo_head', array( $yoast, 'adjacent_rel_links' ), 21 );
		// per Yoast code, this is priority 22
		// remove_action( 'wpseo_head', array( $yoast, 'publisher' ), 22 );
		add_action( 'wpseo_head', 'seadev_add_noindex_metadata' );
	}
    
}
add_action( 'init', 'seadev_remove_robot_metadata' );

/**
 * Increase size cache of autoptimize plugin
 */
add_filter('autoptimize_filter_cachecheck_maxsize','adjust_cachesize');
function adjust_cachesize() {
	return 100*512*1024;
}

/**
 * Add Missing Alt Tags To content
 */
function seadev_auto_add_alt_tags($content) {
    global $post;
    preg_match_all('/<img (.*?)/', $content, $images);
    if(!is_null($images)) {
        foreach($images[1] as $index => $value)
        {
            if(!preg_match('/alt=/', $value))
            {
                $new_img = str_replace('<img', '<img alt="'.$post->post_title.'"', $images[0][$index]);
                $content = str_replace($images[0][$index], $new_img, $content);
            }
        }
    }
    return $content;
}
add_filter('the_content', 'seadev_auto_add_alt_tags', 99999);

/**
 * Set default alt tag image
 */
function seadev_get_image_alt($image, $altDefault = 'Initial Inspiration') {
    if (is_array($image)) {
        if (isset($image['alt']) && $image['alt'] != '') {
            return $image['alt'];
        } else {
            $alt = str_replace('-', ' ', $image['title']);
            return $alt;
        }
    }
    if (is_numeric($image)) {
        $image_id = get_post_thumbnail_id($image);
        $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        if ($alt != '') {
            return $alt;
        } else {
            return str_replace('-', ' ', get_post($image_id)->post_title);
        }
    }
    if(is_string($image)) {
        $image_id = attachment_url_to_postid($image);
        $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        if ($alt != '') {
            return $alt;
        } else {
            return str_replace('-', ' ', get_post($image_id)->post_title);
        }
    }
    return $altDefault;
}