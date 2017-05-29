<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package agencystrap
 */

if ( ! is_active_sidebar( 'sidebar-2' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">

	<?php dynamic_sidebar( 'sidebar-2' ); ?>
</div><!-- #secondary -->
