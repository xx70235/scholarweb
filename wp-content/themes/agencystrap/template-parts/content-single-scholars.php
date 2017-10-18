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

	<div class="entry-content scholar-info" itemprop="text">
        <div class="scholar-meta">
		<div id="update-time">
            <span class="update-time" ><h4> <?php pll_e('研究领域：');$terms = wp_get_post_terms( $post->ID, array( 'research_field') );?>
	            <?php foreach ( $terms as $term ) : ?>
	            <?php echo $term->name.'  '; ?>
	            <?php endforeach; ?></h4>
            </span></div>
        <div id="location">
            <span class="location" ><h4><?php pll_e('隶属机构：'); $terms = wp_get_post_terms( $post->ID, array( 'institute_belong') ); ?>
		            <?php foreach ( $terms as $term ) : ?>
			            <?php echo $term->name.'  '; ?>
		            <?php endforeach; ?></h4>
            </span></div>
        <div id="job-title">
            <span class="job-title" ><h4><?php pll_e('人才计划：');  $terms = wp_get_post_terms( $post->ID, array( 'scholar_project') ); ?>
		            <?php foreach ( $terms as $term ) : ?>

			            <?php echo $term->name.'  '; ?>
		            <?php endforeach; ?></h4>
            </span></div>
            <div id="title">
                <span class="title" ><h4><?php pll_e('职称：'); ?></h4></span>
		        <?php the_field(title);?>
            </div>
            <div id="email">
                <span class="email" ><h4><?php pll_e('邮箱：'); ?></h4></span>
		        <?php the_field(email);?>
            </div>
            <div id="webpage">
                <span class="webpage" ><h4><?php pll_e('主页：'); ?></h4></span>
		        <?php the_field(webpage);?>
            </div>
            <div id="address">
                <span class="address" ><h4><?php pll_e('通讯地址：'); ?></h4></span>
		        <?php the_field(address);?>
            </div>
            <div id="intro">
                <span class="intro" ><h4><?php pll_e('个人介绍：'); ?></h4></span>
		        <?php the_field(intro);?>
            </div>
            <div id="education">
                <span class="education" ><h4><?php pll_e('教育经历：'); ?></h4></span>
		        <?php the_field(education);?>
            </div>
            <div id="work">
                <span class="work" ><h4><?php pll_e('工作经历：'); ?></h4></span>
		        <?php the_field(work);?>
            </div>
            <div id="research">
                <span class="research" ><h4><?php pll_e('研究方向：'); ?></h4></span>
		        <?php the_field(research);?>
            </div>
            <div id="honor">
                <span class="honor" ><h4><?php pll_e('荣誉奖励：'); ?></h4></span>
		        <?php the_field(honor);?>
            </div>
            <div id="paper">
                <span class="paper" ><h4><?php pll_e('代表性论文：'); ?></h4></span>
		        <?php the_field(paper);?>
            </div>
    </div>
        <div class="scholar-image">
            <?php  $source_image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') );
            echo '<img src="';
            echo $source_image_url;
            echo '" alt="';the_title();
            echo '" width="150px" />'; ?>
        </div>



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
