<?php
/**
 * WooCommerce Category-related
 *
 * @package woocommerce
 */

// Remove category counts.
add_filter( 'woocommerce_subcategory_count_html', '__return_null' );

add_filter( 'pre_get_posts', 'exclude_product_cat_children' );
function exclude_product_cat_children( $wp_query ) {
	if ( isset( $wp_query->query_vars['product_cat'] ) && $wp_query->is_main_query() ) {
		$wp_query->set(
			'tax_query',
			array(
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'slug',
					'terms'            => $wp_query->query_vars['product_cat'],
					'include_children' => false,
				),
			)
		);
	}
}
