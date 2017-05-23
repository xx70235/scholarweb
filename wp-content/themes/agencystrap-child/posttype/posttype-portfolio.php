<?php

/*-----------------------------------------------------------------------------------
/*	Add Portfolio case studies Post Type
/*---------------------------------------------------------------------------------*/

function agencystrap_post_type_portfolio()
{
	$args = array(
		'labels' => array(
        'name' => __( 'Portfolio' ),
        'singular_name' => __( 'Portfolio' )
        ),
		'public' => true,
		'has_archive' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'hierarchical' => false,
		'menu_position' => 11,
		'menu_icon'   => 'dashicons-clipboard',
		// Uncomment the folowing line to change the slug
		//'rewrite' => array( 'slug' => 'portfolio-slug' ),
		'supports' => array('title','editor','thumbnail','custom-fields', 'page-attributes')
	);

	register_post_type( 'portfolio', $args );
}
add_action( 'init', 'agencystrap_post_type_portfolio', 1 );

/*--------------------------------------------------------------------------------*/
/*  Add Custom Columns for Portfolios
/*--------------------------------------------------------------------------------*/
function agencystrap_edit_columns_portfolio($portfolio_columns){
	$portfolio_columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => _x( 'Title', 'column name', 'agencystrap' ),
		'thumbnail' => __( 'Thumbnail', 'agencystrap')
	);

	return $portfolio_columns;
}
add_filter('manage_edit-portfolio_columns', 'agencystrap_edit_columns_portfolio');

function agencystrap_custom_columns_portfolio($portfolio_columns, $post_id){

	switch ($portfolio_columns) {
	    case 'thumbnail':
	        $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );

	        if( $thumbnail_id ) {
	            $thumb = wp_get_attachment_image( $thumbnail_id, 'admin-thumb', true );
	        }

	        if( isset($thumb) ) {
	           echo $thumb;
	        } else {
	            echo __('None', 'agencystrap');
	        }

	        break;

		default:
			break;
	}
}
add_action('manage_posts_custom_column',  'agencystrap_custom_columns_portfolio', 10,2);
?>
