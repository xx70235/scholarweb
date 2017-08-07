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
		<?php the_title( sprintf( '<h3 class="entry-title" itemprop="headline"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>

	</header><!-- .entry-header -->

	<div class="entry-content" itemprop="text">
<?php //the_excerpt(); ?>
        <div id="entry-parameters">
           <i class="fa fa-graduation-cap"></i> <span class="discipline" >学科：
	            <?php //the_excerpt();
	            // your taxonomy name
	            $tax = 'first_level_discipline';

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
            </span>
          <i class="fa fa-map-marker"></i> <span class="location" >地点：
	            <?php //the_excerpt();
	            // your taxonomy name
	            $tax = 'location';

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
            </span>
           <i class="fa fa-rocket" aria-hidden="true"></i> <span class="views" >关注量：

                <?php
                $view =getPostViews($post->ID);
                echo $view.' ';
                ?></span>
          <i class="fa fa-calendar-times-o" aria-hidden="true"></i>  <span class="deadline" >截止日期：
                <?php
                echo strip_tags(get_field(deadline_time));
                ?>
            </span>
        </div>
	</div><!-- .entry-content -->
<!--        <div class="entry-meta">-->
<!--        <div class="image-author">-->
<!--        --><?php //echo get_avatar( get_the_author_meta( 'email' ), '42' ); ?>
<!--        </div>-->
<!--        <div class="detail-author-date">-->
<!--        <span class="author-word" itemscope="itemscope" itemtype="http://schema.org/Person" itemprop="author">-->
<!--        <span class="author vcard" itemprop="name">-->
<!--        --><?php //echo get_the_author() ?>
<!--        </span>-->
<!--        </span>-->
<!--        <span class="anything-devider">|</span>-->
<!--	--><?php //agencystrap_posted_on(); ?>
<!--	</div>-->
<!--	</div>-->
    </div>
    <div class="clear"></div>
</article>
<!-- #post-## -->
