<?php

/*-----------------------------------------------------------------------------------
/*	Add Portfolio case studies Post Type
/*---------------------------------------------------------------------------------*/

function agencystrap_post_type_testimonials()
{
	$args = array(
		'labels' => array(
        'name' => __( 'Testimonials' ),
        'singular_name' => __( 'Testimonial' )
        ),
		'public' => true,
		'has_archive' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'hierarchical' => false,
		'menu_position' => 12,
		'menu_icon'   => 'dashicons-heart',
		// Uncomment the folowing line to change the slug
		//'rewrite' => array( 'slug' => 'portfolio-slug' ),
		'supports' => array('title','excerpt','thumbnail', 'page-attributes')
	);

	register_post_type( 'testimonials', $args );
}
add_action( 'init', 'agencystrap_post_type_testimonials', 1 );


/*--------------------------------------------------------------------------------*/
/*  Add Custom Columns for Testimonials
/*--------------------------------------------------------------------------------*/
function agencystrap_edit_columns_testimonial($testimonial_columns){
	$testimonial_columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => _x( 'Title', 'column name', 'agencystrap' ),
		'thumbnail' => __( 'Thumbnail', 'agencystrap')
	);

	return $testimonial_columns;
}
add_filter('manage_edit-testimonial_columns', 'agencystrap_edit_columns_testimonial');

function agencystrap_custom_columns_testimonial($testimonial_columns, $post_id){

	switch ($testimonial_columns) {
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
add_action('manage_posts_custom_column',  'agencystrap_custom_columns_testimonial', 10, 2);
?>
