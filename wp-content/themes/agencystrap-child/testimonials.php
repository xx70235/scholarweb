<?php
/**
 *Template Name: Testimonials
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
      <div class="blog-content">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">
            <?php
		  $args = array(
			'post_type' => 'testimonials',
		);
    $loop = new WP_Query( $args );?>
        <div class="testimonial-list-page">
        <ul class="testimonial">
    <?php while ( $loop->have_posts() ) : $loop->the_post();
    ?>
            <li>
                <div class="testi-thumb"><?php the_post_thumbnail('portfolio-icon'); ?></div>
                <div class="testi-content">
                <?php the_excerpt(); ?>
                    <span>-<?php the_title(); ?></span>
                </div>
                <div class="clr"></div>

            </li>

    <?php endwhile;?> </ul> </div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php do_action("agencystrap_page_after");?>
<?php get_sidebar(); ?>

</div>
<?php get_footer(); ?>
