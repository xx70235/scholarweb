<?php
/**
 * The template for displaying all single posts.
 *
 * @package agencystrap
 */

get_header(); ?>

	<div id="primary" class="content-area content-area-full">
		<main id="main" class="site-main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template-parts/content', 'single-enroll' ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
			?>
           
		<?php endwhile; // end of the loop. ?>
           <?php agencystrap_postnav();?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php get_footer(); ?>
