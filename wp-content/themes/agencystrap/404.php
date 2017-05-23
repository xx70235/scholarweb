<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package agencystrap
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h3 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'agencystrap' ); ?></h3>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'agencystrap' ); ?></p>

					<?php get_search_form(); ?>

					<?php //the_widget( 'WP_Widget_Recent_Posts' ); ?>


				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->
<?php get_footer(); ?>
