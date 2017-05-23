<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package agencystrap
 */
?>

</div><!-- #content -->
    <?php do_action("agencystrap_footer_top");?>
    <?php  agencystrap_threecolumnfooter(); ?>
    <?php do_action("agencystrap_footer_bottom");?>
    <?php wp_footer(); ?>

</body>
</html>
