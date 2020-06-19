<!-- The video -->
<video autoplay muted loop id="myVideo">
  <source src="C:\Users\cuong.phan\Desktop\video\Field for website.mp4" type="video/mp4">
</video>

<!-- Optional: some overlay text to describe the video -->
<div class="content">
  <h1>Heading</h1>
  <p>Lorem ipsum...</p>
  <!-- Use a button to pause/play the video with JavaScript -->
  <button id="myBtn" onclick="myFunction()">Pause</button>
</div>


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