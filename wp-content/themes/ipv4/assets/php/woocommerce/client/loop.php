<?php
/**
 * WooCommerce Product Loop
 * Client-specific
 *
 * @package woocommerce
 */

// Adjust WooCommerce shop products per page.
add_action( 'woocommerce_product_query', 'mp_wc_products_query' );
function mp_wc_products_query( $query ) {
	$query->set( 'posts_per_page', -1 );
}
