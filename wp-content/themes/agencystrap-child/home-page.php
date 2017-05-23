<?php
/**
 *Template Name: Home Page
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
        <?php agencystrap_aboutcta(); ?>
        <?php agencystrap_casestudy(); ?>
        <?php agencystrap_iconcontent(); ?>
        <?php agencystrap_rightimagecta(); ?>
        <?php agencystrap_home_testimonials(); ?>
    <?php do_action("agencystrap_page_after");?>
<?php get_footer(); ?>
