<?php
/**
 * The template for displaying all single posts.
 *
 * @package agencystrap
 */

get_header(); ?>
<div class="archive-content">
	<div id="primary" class="content-area ">
		<main id="main" class="site-main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">

		<?php while ( have_posts() ) : the_post(); ?>

<?php echo "<input id='post-id' value='".$post->ID."' style='display:none;'/>";?>
			<?php get_template_part( 'template-parts/content', 'single-news' ); ?>

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
	<?php get_sidebar('2'); ?>
</div>
<script type="text/javascript">
jQuery(function(){ 
        jQuery(document.body).on('click','.social-share-icon',function(){
                var id = jQuery("#post-id").val();
                jQuery.post("/wp-admin/admin-ajax.php",
          {
              pid:id,
              action: 'socialCount'
          },
          function(data,status){
              if ('success'==status)
              {
                  
              }
              else
              {
                  
              }
          });
                
        })
});
</script>
<?php get_footer(); ?>
