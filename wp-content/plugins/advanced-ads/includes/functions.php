<?php

/*
 * functions that are directly available in WordPress themes (and plugins)
 */

/**
 * return ad content
 *
 * @since 1.0.0
 * @param int $id id of the ad (post)
 * @param arr $args additional arguments
 */
function get_ad($id = 0, $args = array()){
	if ( defined( 'ADVANCED_ADS_DISABLE_CHANGE' ) && ADVANCED_ADS_DISABLE_CHANGE ) {
		$args = array();
	}

	return Advanced_Ads_Select::get_instance()->get_ad_by_method( $id, 'id', $args );
}

/**
 * echo an ad
 *
 * @since 1.0.0
 * @param int $id id of the ad (post)
 * @param arr $args additional arguments
 */
function the_ad($id = 0, $args = array()){
	echo get_ad( $id, $args );
}

/**
 * return an ad from an ad group based on ad weight
 *
 * @since 1.0.0
 * @param int $id id of the ad group (taxonomy)
 *
 */
function get_ad_group( $id = 0, $args = array() ) {
	if ( defined( 'ADVANCED_ADS_DISABLE_CHANGE' ) && ADVANCED_ADS_DISABLE_CHANGE ) {
		$args = array();
	}
	return Advanced_Ads_Select::get_instance()->get_ad_by_method( $id, 'group', $args );
}

/**
 * echo an ad from an ad group
 *
 * @since 1.0.0
 * @param int $id id of the ad (post)
 */
function the_ad_group($id = 0){
	echo get_ad_group( $id );
}

/**
 * return content of an ad placement
 *
 * @since 1.1.0
 * @param string $id slug of the ad placement
 *
 */
function get_ad_placement( $id = '', $args = array() ) {
	if ( defined( 'ADVANCED_ADS_DISABLE_CHANGE' ) && ADVANCED_ADS_DISABLE_CHANGE ) {
		$args = array();
	}
	return Advanced_Ads_Select::get_instance()->get_ad_by_method( $id, 'placement', $args );
}

/**
 * return content of an ad placement
 *
 * @since 1.1.0
 * @param string $id slug of the ad placement
 */
function the_ad_placement($id = ''){
	echo get_ad_placement( $id );
}

/**
 * return true if ads can be displayed
 *
 * @since 1.4.9
 * @return bool, true if ads can be displayed
 */
function advads_can_display_ads(){
    return Advanced_Ads::get_instance()->can_display_ads();
}
