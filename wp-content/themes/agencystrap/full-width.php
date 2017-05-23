<?php
/**
 * Template Name: Full width
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package agencystrap
 */

get_header(); ?>

<?php do_action("agencystrap_full_content_before");?>
	<div id="primary" class="content-area content-area-full">
		<main id="main" class="site-main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'page' ); ?>

				<?php
//If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || get_comments_number() ) :
				        comments_template();
					endif;
				?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->
<?php do_action("agencystrap_full_content_after");?>
<?php get_footer(); ?>
