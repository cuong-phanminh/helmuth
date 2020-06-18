<?php
/**
 * This is the template that renders the background slider block.
 *
 * @param   array $block The block settings and attributes.
 * @param   bool $is_preview True during AJAX preview.
 */

 
$block_id = $block['id'];
$block_category = $block['category'];
$block_class = (isset($block['className'])) ? $block['className'] : '';
$block_name = str_replace("acf/", "", $block['name']);
$block_classes_arr = array( $block_category, $block_class );
$block_classes = implode(" ", $block_classes_arr);

$height = get_field( 'seadev_block_background_slider_height' );
$height_custom = get_field( 'seadev_block_background_slider_height_custom' );

$height_style = "";
if ($height == "full") {
  $height_style = "height:100vh;";
} else {
  $height_style = "height:".$height_custom."px;";
}

$content_style = "";

$opacity = get_field( 'seadev_block_background_slider_overlay_opacity' );
$content_style .= "background-color:rgba(0,0,0," . $opacity/100 . ");";

$text_color = get_field( 'seadev_block_background_slider_text_color' );
$content_style .= "color:".$text_color.";";


$content_classes_arr = array('d-flex', 'flex-column', 'flex-wrap');

$horizontal_align = get_field( 'seadev_block_background_slider_horizontal_text_align' );
if ($horizontal_align == 'left') {
  array_push($content_classes_arr, 'justify-content-start');
} elseif ($horizontal_align == 'right') {
  array_push($content_classes_arr, 'justify-content-end');
} else {
  array_push($content_classes_arr, 'justify-content-center');
}

$vertical_align = get_field( 'seadev_block_background_slider_vertical_text_align' );
if ($vertical_align == 'top') {
  array_push($content_classes_arr, 'align-items-start');
} elseif ($vertical_align == 'bottom') {
  array_push($content_classes_arr, 'align-items-end');
} else {
  array_push($content_classes_arr, 'align-items-center');
}

$content_classes = implode(" ", $content_classes_arr);

// Slider controls
$show_dots = (get_field( 'seadev_block_background_slider_show_dots' ) == '1') ? "true" : "false";
$show_arrows = (get_field( 'seadev_block_background_slider_show_arrows' ) == '1') ? "true" : "false";
$infinite = (get_field( 'seadev_block_background_slider_infinite' ) == '1') ? "true" : "false";
$fade = (get_field( 'seadev_block_background_slider_mode' ) == 'fade') ? "true" : "false";
$autoplay = (get_field( 'seadev_block_background_slider_autoplay' ) == '1') ? "true" : "false";
$autoplay_speed = (get_field( 'seadev_block_background_slider_autoplay_speed' ) != '') ? get_field( 'seadev_block_background_slider_autoplay_speed' ) : 4000;
$transition_speed = (get_field( 'seadev_block_background_slider_transition_speed' ) != '') ? get_field( 'seadev_block_background_slider_transition_speed' ) : 600;

?>

<?php if ( have_rows( 'seadev_block_background_slider_items' ) ) : ?>

<div id="<?php echo $block_id; ?>" class="seadev-background-slider <?php echo $block_classes; ?>" style="<?php echo $height_style; ?>">

  <div class="slider-content <?php echo $content_classes; ?>" style="<?php echo $content_style; ?>">
    <?php the_field( 'seadev_block_background_slider_content' ); ?>
  </div>
  <div class="slider-wrapper">

  <?php while ( have_rows( 'seadev_block_background_slider_items' ) ) : the_row(); ?>

    <?php
      $slider_type = get_sub_field( 'seadev_block_background_slider_type' );
      $background_image = get_sub_field( 'seadev_block_background_slider_image' );
      $youtube_video_id = get_sub_field('seadev_block_background_slider_youtube');
      $vimeo_video_id = get_sub_field('seadev_block_background_slider_vimeo');
      $video = get_sub_field( 'seadev_block_background_slider_video' );
    ?>
    
    <?php if ( $slider_type == 'video' ) : ?>

      <div class="slide-item background-video video">
        <video class="slide-video slide-media" loop muted preload="metadata" poster="">
          <source src="<?php echo $video['url']; ?>" type="video/mp4" />
        </video>
      </div>

    <?php elseif ( $slider_type == 'youtube' ) : ?>

      <div class="slide-item background-video youtube">
        <iframe class="embed-player" src="https://www.youtube.com/embed/<?php echo $youtube_video_id; ?>?enablejsapi=1&controls=0&fs=0&iv_load_policy=3&rel=0&showinfo=0&loop=1&Q&start=1&playlist=<?php echo $youtube_video_id; ?>" frameborder="0" allowfullscreen></iframe>
      </div>

    <?php elseif ( $slider_type == 'vimeo' ) : ?>

      <div class="slide-item background-video vimeo">
        <iframe class="embed-player slide-media" src="https://player.vimeo.com/video/<?php echo $vimeo_video_id; ?>?api=1&byline=0&portrait=0&title=0&background=1&mute=1&loop=1&autoplay=0id=<?php echo $vimeo_video_id; ?>" width="980" height="520" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
      </div>

    <?php else : ?>

      <div class="slide-item background-image">
        <div class="slide-item-inner" style="background-image: url(<?php echo $background_image['url']; ?>)"></div>
      </div>

    <?php endif; ?>

  <?php endwhile; ?>  

  </div>
</div>

<script type="text/javascript">
  (function($) {
    "use strict";
    
    // Callback to initialize the block.
    function initializeBlock( $block ) {

      var slideWrapper = $block.find(".slider-wrapper");
      var iframes = slideWrapper.find('.embed-player');

      // Initialize
      slideWrapper.on("init", function(slick){
        slick = $(slick.currentTarget);
        setTimeout(function(){
          playPauseVideo(slick,"play");
        }, 1000);
        resizePlayer(slideWrapper, iframes, 16/9);
      });
      slideWrapper.on("beforeChange", function(event, slick) {
        slick = $(slick.$slider);
        playPauseVideo(slick,"pause");
      });
      slideWrapper.on("afterChange", function(event, slick) {
        slick = $(slick.$slider);
        playPauseVideo(slick,"play");
      });
      
      slideWrapper.not('.slick-initialized').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        speed: <?php echo $transition_speed; ?>,
        autoplay: <?php echo $autoplay; ?>,
        autoplaySpeed: <?php echo $autoplay_speed; ?>,
        infinite: <?php echo $infinite; ?>,
        fade: <?php echo $fade; ?>,
        dots: <?php echo $show_dots; ?>,
        arrows: <?php echo $show_arrows; ?>
      });

      // // Resize event
      $(window).on("resize.slickVideoPlayer", function(){  
        resizePlayer(slideWrapper, iframes, 16/9);
      });

    }

    // Initialize each block on page load (front end).
    $(document).ready(function(){
      // $('.seadev-background-slider').each(function(){
      //   initializeBlock( $(this) );
      // });
      initializeBlock( $("#<?php echo $block_id ?>") );
    });

    // // Initialize block preview (editor).
    if( window.acf ) {
      window.acf.addAction( 'render_block_preview', initializeBlock );
    }

    function postMessageToPlayer(player, command){
      if (player == null || command == null) return;
      player.contentWindow.postMessage(JSON.stringify(command), "*");
    }

    function playPauseVideo(slick, control){
      var currentSlide, startTime, player, video;

      currentSlide = slick.find(".slick-current");
      player = currentSlide.find("iframe").get(0);
      startTime = currentSlide.data("video-start");

      if (currentSlide.hasClass('vimeo')) {
        switch (control) {
          case "play":
            if ((startTime != null && startTime > 0 ) && !currentSlide.hasClass('started')) {
              currentSlide.addClass('started');
              postMessageToPlayer(player, {
                "method": "setCurrentTime",
                "value" : startTime
              });
            }
            postMessageToPlayer(player, {
              "method": "play",
              "value" : 1
            });
            break;
          case "pause":
            postMessageToPlayer(player, {
              "method": "pause",
              "value": 1
            });
            break;
        }
      } else if (currentSlide.hasClass('youtube')) {
        switch (control) {
          case "play":
            postMessageToPlayer(player, {
              "event": "command",
              "func": "mute"
            });
            postMessageToPlayer(player, {
              "event": "command",
              "func": "playVideo"
            });
            break;
          case "pause":
            postMessageToPlayer(player, {
              "event": "command",
              "func": "pauseVideo"
            });
            break;
        }
      } else if (currentSlide.hasClass('video')) {
        video = currentSlide.children("video").get(0);
        if (video != null) {
          if (control === "play"){
            video.play();
          } else {
            video.pause();
          }
        }
      }
    }

    // Resize player
    function resizePlayer(slider, iframes, ratio) {
      if (!iframes[0]) return;
      var win = slider,
          width = win.width(),
          playerWidth,
          height = win.height(),
          playerHeight,
          ratio = ratio || 16/9;

      iframes.each(function(){
        var current = $(this);
        if (width / ratio < height) {
          playerWidth = Math.ceil(height * ratio);
          current.width(playerWidth).height(height).css({
            left: (width - playerWidth) / 2,
            top: 0
            });
        } else {
          playerHeight = Math.ceil(width / ratio);
          current.width(width).height(playerHeight).css({
            left: 0,
            top: (height - playerHeight) / 2
          });
        }
      });
    }

  }(jQuery));
</script>

<?php endif; ?>
