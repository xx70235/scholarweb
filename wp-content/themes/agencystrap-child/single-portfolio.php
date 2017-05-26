<?php
/*
 * template name: single portfolio 
Page displays single portfolio Items
*/
?>

<?php get_header(); ?>
	<?php if( have_posts() ) :
		echo '<div class="port-content-page"><div id="primary" class="hfeed">';
		while( have_posts() ) : the_post();
		?>
<!--BEGIN .hentry-->
<div <?php post_class( $style . ' ' . $media_pos ) ?> id="post-<?php the_ID(); ?>">

                <?php
	                if ( get_the_post_thumbnail() != '' ) {
	                echo '<div class="image-container" itemprop="image">';
	                $source_image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') );
	                echo '<img src="';
	                echo $source_image_url;
	                echo '" alt="';the_title();
	                echo '" /></div>';
	                }
                ?>
							<?php the_content(); ?>

							<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages:', 'agencystrap').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

							<?php if( !empty($portfolio_url) && ( $media_pos == 'agencystrap-media-left' || $media_pos == 'agencystrap-media-right' ) ) { ?>
								<a href="<?php echo esc_url($portfolio_url); ?>" class="more-link"><?php echo $portfolio_button_copy; ?></a>
							<?php } ?>
						<!--END .entry-content -->
						</div>
						<!--BEGIN .entry-media -->
						<div class="entry-media">
							<?php
							if( $portfolio_display_gallery == 'on' ) {
								$gallery_layout = get_post_meta( $post->ID, '_agencystrap_gallery_layout', true);
								$slideshow = ( $gallery_layout == 'slideshow' ) ? true : false;
								$size = ( $media_pos == 'agencystrap-media-center' ) ? 'portfolio-full' : 'portfolio-index';
								agencystrap_gallery( $post->ID, $size, $slideshow, $slideshow );
							}

							if( $portfolio_display_video == 'on' ) {
								$embed = get_post_meta($post->ID, '_agencystrap_video_embed_code', true);
						        if( !empty( $embed ) ) {
						            echo stripslashes(htmlspecialchars_decode($embed));
						        } else {
						            agencystrap_video( $post->ID, $width );
						        }
							}
							if( $portfolio_display_audio == 'on' ) {
								agencystrap_audio( $post->ID, $width );
							}
							?>
						<!--END .entry-media -->
						</div>
			<?php endwhile; ?>
			<!--END #primary .hfeed-->
			</div>
    	<?php else: ?>
    	<div id="content">
			<!--BEGIN #post-0-->
			<div id="post-0" <?php post_class(); ?>>
				<h2 class="entry-title"><?php _e('Error 404 - Not Found', 'agencystrap') ?></h2>
				<!--BEGIN .entry-content-->
				<div class="entry-content">
					<p><?php _e("Sorry, but you are looking for something that isn't here.", "agencystrap") ?></p>
				<!--END .entry-content-->
				</div>
	    <?php endif; ?>
                <?php get_sidebar(); ?></div>
<?php get_footer(); ?>
