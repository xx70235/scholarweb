<?php
/**
 *Template Name: Ad Home 
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package agencystrap
 */

get_header(); ?>
<div class="blog-content">
<div style='padding-top:30px;'>
<?php 
echo do_shortcode("[metaslider id=131]"); 
?>
</div>
<div style='padding-top:30px;'>
<?php 
echo do_shortcode("[metaslider id=134]"); 
?>
</div>
<div style='padding-top:30px;'>
<?php 
echo do_shortcode("[metaslider id=138]"); 
?>
</div>
<div style='padding-top:30px;'>
<?php 
echo do_shortcode("[metaslider id=141]"); 
?>
      </div><!-- #primary -->
  </div>
<?php get_footer(); ?>
