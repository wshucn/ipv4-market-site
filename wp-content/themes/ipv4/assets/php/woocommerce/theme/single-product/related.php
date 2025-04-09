<?php
/**
 * WooCommerce Related Products
 *
 * @package woocommerce
 */

// Disable Related Products search by tags/categories
// add_filter( 'woocommerce_get_related_product_cat_terms', '__return_empty_array');
// add_filter( 'woocommerce_get_related_product_tag_terms', '__return_empty_array');

// Exclude Related Products that are from parent categories
// (i.e., do not search up the category hierarchy for related products)
// add_filter( 'woocommerce_related_products', 'mp_exclude_parent_related_products', 10, 3 );
function mp_exclude_parent_related_products( $related_posts, $product_id, $args ) {
	if ( empty( $product_id ) || empty( $related_posts ) ) {
		return $related_posts;
	}

	$cat      = get_the_terms( $product_id, 'product_cat' );
	$cat_ids  = wp_list_pluck( $cat, 'term_id' );
	$terms    = get_terms(
		'product_cat',
		array( 'parent' => 0 )
	);
	$term_ids = wp_list_pluck( $terms, 'term_id' );

	// Don't do related posts on child category products
	$is_child_cat = array_intersect( $cat_ids, $term_ids );
	if ( empty( $is_child_cat ) ) {
		return array();
	}

	$args        = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'tax_query'      => array(
			array(
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => $term_ids,
				'operator'         => 'IN',
				'include_children' => false,
			),
		),
	);
	$products    = new WP_Query( $args );
	$exclude_ids = wp_list_pluck( $products->posts, 'ID' );

	return array_diff( $related_posts, $exclude_ids );
}

// Change number of related products output
add_filter( 'woocommerce_output_related_products_args', 'mp_wc_output_related_products_args', 20 );
function mp_wc_output_related_products_args( $args ) {
	$args['posts_per_page'] = 999;
	$args['columns']        = 3;
	return $args;
}
