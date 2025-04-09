<?php
/**
 * WooCommerce Gutenberg blocks
 *
 * @package woocommerce
 */


/**
 * Patch WooCommerce Product Grid Block SQL error
 *
 * @link https://wordpress.org/support/topic/product-grid-blocks-database-error/
 */
add_filter( 'query', 'return_fake_variation_id' );
function return_fake_variation_id( $sql ) {
	if ( str_contains( wp_debug_backtrace_summary(), 'prime_product_variations' ) ) {
		return "SELECT '' as variation_id FROM DUAL";
	}
	return $sql;
}
