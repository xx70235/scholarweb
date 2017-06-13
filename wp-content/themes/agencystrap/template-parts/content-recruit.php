<?php
/**
 * @package agencystrap
 */
?>
<!--<div class="article-block">-->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting" itemprop="blogPost">

<?php
if ( get_the_post_thumbnail() != '' ) {
echo '<div class="image-container" itemprop="image"><a href="'; the_permalink(); echo '" class="thumbnail-wrapper">';
$source_image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') );
echo '<img src="';
echo $source_image_url;
echo '" alt="';the_title();
echo '" width="150px" height="150px" />';
echo '</a></div>';
}
?>
    <div class="article-info">
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title" itemprop="headline"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

	</header><!-- .entry-header -->

	<div class="entry-content" itemprop="text">
<?php //the_excerpt(); ?>
        <div id="keywords">
            <span class="keywords" >关键字：
	            <?php //the_excerpt();
	            // your taxonomy name
	            $tax = 'keywords';

	            // get the terms of taxonomy
	            $terms = wp_get_post_terms($post->ID, $tax, [
		            'hide_empty' => false, // do not hide empty terms
	            ]);

	            // loop through all terms
	            foreach( $terms as $term ) {

		            // if no entries attached to the term
		            if( 0 == $term->count )
			            // display only the term name
			            echo  $term->name . '  ';

		            // if term has more than 0 entries
                    elseif( $term->count > 0 )
			            // display link to the term archive
			            echo '<a href="'. get_term_link( $term ) .'">'. $term->name.'  ' .'</a>';

	            }
	            ?>
            </span></div>
<!--        <a href="--><?php //the_permalink(); ?><!-- " class="btn read-more">阅读全文</a>-->
	</div><!-- .entry-content -->
        <div class="entry-meta">
        <div class="image-author">
        <?php echo get_avatar( get_the_author_meta( 'email' ), '42' ); ?>
        </div>
        <div class="detail-author-date">
        <span class="author-word" itemscope="itemscope" itemtype="http://schema.org/Person" itemprop="author">
<!--       <a href="--><?php //echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?><!--" rel="author">-->
        <span class="author vcard" itemprop="name">
        <?php echo get_the_author() ?>
        </span>
<!--        </a>-->
        </span>
        <span class="anything-devider">|</span>
	<?php agencystrap_posted_on(); ?>
	</div>
	</div>
    </div>
    <div class="clear"></div>
</article>
<!-- #post-## -->