<?php
/**
 * template name: archive
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package agencystrap
 */

get_header(); ?>
<div class="archive-content">
    <?php echo do_shortcode( '[searchandfilter fields="job_title,location,keywords" submit_label="筛选" types="radio,radio,radio" headings="职位/学位,工作地点,关键词"]' );?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h3 class="page-title">', '</h3>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', 'recruit' );

				?>

			<?php endwhile; ?>


<div class="paging">
        <?php the_posts_pagination(); ?>
</div>
		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
