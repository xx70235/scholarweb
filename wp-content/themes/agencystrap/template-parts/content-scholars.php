<?php
/**
 * @package agencystrap
 */
?>
<div class="scholars-div">
<article  id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting" itemprop="blogPost">

<?php
if ( get_the_post_thumbnail() != '' ) {
echo '<div class="image-container-scholars" itemprop="image"><a href="'; the_permalink(); echo '" class="thumbnail-wrapper">';
$source_image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') );
echo '<img src="';
echo $source_image_url;
echo '" alt="';the_title();
echo '" width="700px" />';
echo '</a></div>';
}
?>

	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title" itemprop="headline"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

	</header><!-- .entry-header -->

	<div class="entry-content" itemprop="text">
<?php //the_excerpt(); ?>
        <div class="scholars-research_field" id="research_field">
            <span class="research_field" >
	            <?php pll_e('研究领域：'); //the_excerpt();
	            // your taxonomy name
	            $tax = 'research_field';

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
        <div class="scholars-institute_belong" id="institute_belong">
            <span class="institute_belong" >
	            <?php pll_e('隶属机构：'); //the_excerpt();
	            // your taxonomy name
	            $tax = 'institute_belong';

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
        <div class="scholars-scholar_project" id="入选计划">
            <span class="scholar_project" >
	            <?php pll_e('入选计划：'); //the_excerpt();
	            // your taxonomy name
	            $tax = 'scholar_project';

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
        <a href="<?php the_permalink(); ?> " class="btn read-more"><?php pll_e('更多'); ?></a>
	</div><!-- .entry-content -->

	
</article><!-- #post-## -->
</div>