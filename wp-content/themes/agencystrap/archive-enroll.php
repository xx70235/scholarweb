<?php
/**
 * template name: archive
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package agencystrap
 */

get_header(); ?>
<div class="archive-content">
<?php  global $wpdb;  ?> 
<?php
$order = $_REQUEST["order"];
$orderby = $_REQUEST["orderby"];

$datainfo = $wpdb->get_results("select * from wp_terms where term_id in (select term_id from wp_term_taxonomy where taxonomy = 'first_level_discipline' order by term_id)");?>
<style>
.normal_columns_content {
    clear: both;
}
.list {
    font-size: 14px;
    font-weight: 300;
}
.normal_columns_25pers {
    float: left;
    width: 25%;
    min-width: 250px;
    overflow: hidden;
}
.list-item.-ico {
    padding-left: 36px;
    position: relative;
}
.list-item {
    margin-bottom: 25px;
    text-decoration: none;
}
.list-item a {
    color: #505050;
    vertical-align: middle;
    text-decoration: none;
    font-family: gothamssm-book, Arial, Helvetica, sans-serif;
    letter-spacing: -0.015em;
}
.section-title {
    padding-bottom: 22px;
    margin-bottom: 20px;
    font-size: 16px;
    text-transform: uppercase;
    border-bottom: 1px solid #c4c4c4;
    font-weight: normal;
    font-family: gothamssm-medium, Arial, Helvetica, sans-serif;
    letter-spacing: 0.05em;
}
.showhide{
	padding-bottom: 15px;
	margin-bottom: 15px;
}
.fil{
	padding-left:20px;
	margin-bottom: 15px;
}
.list-item.-ico .icon {
    position: absolute;
    left: 0;
    top: 50%;
    margin-top: -11px;
    fill: transparent;
    stroke: #505050;
    width: 23px;
    height: 22px;
}
.icon {
    fill: currentColor;
}
.icon-lantbruk {
    width: 0.92em;
    height: 1em;
    fill: initial;
}
.icon {
    display: inline-block;
}
.icon {
    font-family: "iconfont";
    font-style: normal;
    font-weight: normal;
    text-rendering: auto;
    speak: none;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>
<?php //var_dump($datainfo);exit;?>
<div class='fil'>
<?php
echo '<h3 class="section-title">学科筛选</h3>';
?>
<div class="showhide">
<a href="javascript:void(0)" id="show" style="display:block" onclick="document.getElementById('a').style.height='100%';document.getElementById('hidden').style.display='block';document.getElementById('show').style.display='none';">展开全部学科</a>
<a href="javascript:void(0)" id="hidden" style="display:none;" onclick="document.getElementById('a').style.height='90px';document.getElementById('hidden').style.display='none';document.getElementById('show').style.display='block';">收起全部学科</a>
</div>
<div class="normal_columns_content list" id="a" style="height:90px;width:100%;overflow-y:hidden;">
<?php
	echo "<div class='normal_columns_25pers'>";
	echo '<div class="list-item -ico">';
	echo '<a href="http://www.tschlr.com/enroll/">全部学科';
	echo '<img width="61" height="61" class="icon icon-lantbruk" src="http://www.tschlr.com/wp-content/uploads/iconsets/a.png">';
	echo '</a>';
	echo '</div>';
	echo '</div>';
	$ahead = array(161,109,110,111,118,94,130,129,132);
	$headbox= array();
	foreach($datainfo as $pre_dk => $pre_dv)
	{
		if(in_array(intval($pre_dv->term_id),$ahead))
		{
			$headbox[$pre_dv->term_id] = $pre_dv;
			unset($datainfo[$pre_dk]);
		}
	}
	$midlist = array();
	foreach($ahead as $hk => $hv)
	{
		if(isset($headbox[$hv]))
		{
			$midlist[] = $headbox[$hv];
		}
	}
	$headbox = $midlist;
	$datainfo = array_values($datainfo);
	$datainfo = array_merge($headbox,$datainfo);
?>
<?php foreach($datainfo as $dk => $dv){
	echo "<div class='normal_columns_25pers'>";
	echo '<div class="list-item -ico">';
	echo '<a href="http://www.tschlr.com/enroll/first_level_discipline/'.$dv->slug.'">'.$dv->name;
	if(!empty($dv->icon_id))
	{
		echo '<img width="61" height="61" class="icon icon-lantbruk" src="http://www.tschlr.com/wp-content/uploads/iconsets/'.$dv->icon_id.'.png">';
	}
	else
	{
		echo '<img width="61" height="61" class="icon icon-lantbruk" src="http://www.tschlr.com/wp-content/uploads/iconsets/a.png">';
	}
	echo '</a>';
	echo '</div>';
	echo '</div>';
}?>
</div>
<?php echo '<h3 class="section-title"></h3>';?>
</div>
	<?php //echo do_shortcode( '[searchandfilter fields="first_level_discipline" submit_label="筛选" types="radio"  headings=""]' );?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Blog">

		<?php

        // 在这里排序 要不就对$posts 数组进行排序？
        if (isset($orderby)) {
            if ($orderby == 'deadline') {
                $args = array(
                    'orderby'   => 'meta_value',
                    'meta_key' => $orderby
                );
            } else {
                $args = array(
                    'orderby'   => $orderby,
                );
            }

            if (isset($order)) {
                $args = array_merge($args, array('order' => $order));
            }

            if ($orderby == 'social_count' || $orderby == 'post_views_count') {
                $args = array_merge($args, array(
                    'meta_query'=>array(
                        array(
                            'key'=>$orderby,
                            'type'=>'NUMERIC',
//                        'compare'=>'>=',
//                        'value'=>'0',
                        )
                    ),
                ));
            }

            $args = array_merge($args, $wp_query->query);
            query_posts($args);
        }


        if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h3 class="page-title">', '</h3>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', 'enroll' );
				?>

			<?php endwhile; ?>


<div class="paging">
        <?php the_posts_pagination(); ?>
</div>
		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
