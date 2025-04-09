<?php
/**
 * WooCommerce Product Loop Order By
 * Client-specific
 *
 * @package woocommerce
 */

/**
 * Adds WooCommerce catalog sorting options using postmeta, such as custom fields
 * Tutorial: http://www.skyverge.com/blog/sort-woocommerce-products-custom-fields/
 */

// 1. Set up the arguments.
// add_filter( 'woocommerce_get_catalog_ordering_args', 'mp_wc_get_catalog_ordering_args' );
function mp_wc_get_catalog_ordering_args( $sort_args ) {
	$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	switch ( $orderby_value ) {

		// Name your sortby key whatever you'd like; must correspond to the $sortby in the next function
		case 'date_asc':
			$sort_args['orderby'] = 'meta_value';
			// Sort by meta_value because we're using alphabetic sorting
			$sort_args['order']    = 'asc';
			$sort_args['meta_key'] = 'learnlive_course_date';
			// use the meta key you've set for your custom field, i.e., something like "location" or "_wholesale_price"
			break;

		case 'date_desc':
			$sort_args['orderby'] = 'meta_value';
			// Sort by meta_value because we're using alphabetic sorting
			$sort_args['order']    = 'desc';
			$sort_args['meta_key'] = 'learnlive_course_date';
			// use the meta key you've set for your custom field, i.e., something like "location" or "_wholesale_price"
			break;

		case 'credits':
			$sort_args['orderby'] = 'meta_value_num';
			// We use meta_value_num here because points are a number and we want to sort in numerical order
			$sort_args['order']    = 'desc';
			$sort_args['meta_key'] = 'learnlive_course_credits';
			break;

	}

	return $sort_args;
}


// Add these new sorting arguments to the sortby options on the frontend.
// add_filter( 'woocommerce_default_catalog_orderby_options', 'mp_wc_catalog_orderby' );
// add_filter( 'woocommerce_catalog_orderby', 'mp_wc_catalog_orderby' );
function mp_wc_catalog_orderby( $sortby ) {
	// Clear out the default options
	$sortby = array();

	// Adjust the text as desired
	$sortby['date_asc']  = __( 'Sort by date: most recent', 'woocommerce' );
	$sortby['date_desc'] = __( 'Sort by date: least recent', 'woocommerce' );
	$sortby['credits']   = __( 'Sort by credits', 'woocommerce' );
	return $sortby;
}
