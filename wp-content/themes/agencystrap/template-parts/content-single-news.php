<?php
/**
 * @package agencystrap
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting" itemprop="blogPost">

	<?php
	if ( get_the_post_thumbnail() != '' ) {
		echo '<div class="image-container" itemprop="image"><a href="'; the_permalink(); echo '" class="thumbnail-wrapper">';
		$source_image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') );
		echo '<img src="';
		echo $source_image_url;
		echo '" alt="';the_title();
		echo '" />';
		echo '</a></div>';
	}
	?>


    <header class="entry-header">
		<?php the_title( '<h1 class="entry-title" itemprop="headline">', '</h1>' ); ?>
		<?php if (function_exists('get_the_subtitle')) {?>
            <h2 class="subtitle"><?php get_the_subtitle(); ?></h2>
		<?php } ?>
        <div class="entry-meta">
            <div class="image-author">
				<?php echo get_avatar( get_the_author_meta( 'email' ), '42' ); ?>
            </div>
            <div class="detail-author-date">
        <span class="author-word" itemscope="itemscope" itemtype="http://schema.org/Person" itemprop="author">
       <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>" rel="author">
        <span class="author vcard" itemprop="name">
        <?php echo get_the_author() ?>
        </span>
        </a>
        </span>
                <span class="anything-devider">|</span>
				<?php agencystrap_posted_on(); ?>
            </div>
        </div>
    </header><!-- .entry-header -->

    <div class="entry-content" itemprop="text">
		<?php the_content(); ?>
		<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'agencystrap' ),
			'after'  => '</div>',
		) );
		?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
		<?php agencystrap_entry_footer(); ?>
    </footer><!-- .entry-footer -->

</article><!-- #post-## -->
