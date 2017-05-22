<?php
/**
 *Template Name: Casestudy
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
'post_type' => 'portfolio',
);
$loop = new WP_Query( $args );
?>
  <div class="portfolio-list-page">
  <ul class="portfolio">
<?php while ( $loop->have_posts() ) : $loop->the_post();
?>
      <li>
          <div class="portfolio-thumb"><a href="<?php echo get_post_permalink( get_the_id() ); ?>"><?php the_post_thumbnail('portfolio-icon'); ?></a></div>
          <div class="portfolio-content">

              <h3>  <a href="<?php echo get_post_permalink( get_the_id() ); ?>"><?php the_title(); ?></a></h3>
              <?php the_excerpt(); ?>
              <a href="<?php echo get_post_permalink( get_the_id() ); ?>">Read More</a>
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
