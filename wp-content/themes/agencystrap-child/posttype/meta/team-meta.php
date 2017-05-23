<?php

/**
 * Create the Portfolio meta boxes
 */

add_action('add_meta_boxes', 'agencystrap_metabox_teams');
function agencystrap_metabox_teams(){

    /* Create a settings metabox -----------------------------------------------------*/
    $meta_box = array(
		'id' => 'agencystrap_metabox_teams-settings',
		'title' =>  __('Team Settings', 'agencystrap'),
		'description' => __('Input basic settings for this Team social link.', 'agencystrap'),
		'page' => 'teams',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
					'name' => __('Facebook Profile  link', 'agencystrap'),
					'desc' => __('Enter a feature link', 'agencystrap'),
					'id' => '_agencystrapp_facebook_link',
					'type' => 'text',
					'std' => ''
				)
		)
	);
    agencystrap_add_meta_box( $meta_box );
}
