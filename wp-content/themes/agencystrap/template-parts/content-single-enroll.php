<?php
/**
 * @package agencystrap
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting" itemprop="blogPost">
    <?php setPostViews(get_the_ID()); ?>

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
        <div id="update-time">
            <span class="update-time" ><h4><?php pll_e('更新时间：');?> <?php strip_tags(the_field(update_time));?></h4>

            </span></div>
        <div id="location">
            <span class="location" ><h4><?php pll_e('招生地点：'); $terms = wp_get_post_terms( $post->ID, array( 'location') ); ?>
                    <?php foreach ( $terms as $term ) : ?>
                        <?php echo $term->name.'  '; ?>
                    <?php endforeach; ?></h4>
                <!--	           <p> </p>-->
            </span></div>
        <div id="job-title">
            <span class="job-title" ><h4><?php pll_e('学位：'); $terms = wp_get_post_terms( $post->ID, array( 'job_title') ); ?>
                    <?php foreach ( $terms as $term ) : ?>

                        <?php echo $term->name.'  '; ?>
                    <?php endforeach; ?></h4>
                <!--	            <p></p>-->
            </span></div>
        <div id="keywords">
            <span class="keywords" ><h4> <?php pll_e('关键字：'); $terms = wp_get_post_terms( $post->ID, array( 'keywords') ); ?>
                    <?php foreach ( $terms as $term ) : ?>

                        <?php echo $term->name.'  '; ?>
                    <?php endforeach; ?></h4>
                <!--	          <p> </p>-->
            </span></div>

        <div id="details">
            <span class="details" ><h4><?php pll_e('招生详情：'); ?></h4></span>
            <?php  the_field(details);?>
        </div>
        <div id="deadline">
            <span class="deadline" ><h4><?php pll_e('截止时间：'); ?></h4>
                <?php the_field(deadline_time);?>
                <?php   strip_tags(the_field(deadline));?>
            </span></div>
    </div><!-- .entry-content -->
	<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'agencystrap' ),
				'after'  => '</div>',
			) );
		?>
	<footer class="entry-footer">
		<?php agencystrap_entry_footer(); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->
