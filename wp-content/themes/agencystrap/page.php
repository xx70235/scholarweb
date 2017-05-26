<?php
/**
 * template name: page
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package agencystrap
 */

get_header(); ?>
    <?php do_action("agencystrap_page_before");?>
    <div class="page-content">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">
            <?php do_action("agencystrap_page_content_before");?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'page' ); ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; // end of the loop. ?>
            <?php do_action("agencystrap_page_content_after");?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php do_action("agencystrap_page_after");?>
<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
