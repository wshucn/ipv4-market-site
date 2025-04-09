<?php
/**
 * WooCommerce Product Loop
 *
 * @package woocommerce
 */

// We don't need result counts.
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

// Disable the sorting bar for the shop loop.
// remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


// Override theme default specification for product # per row
// You can have different number of product columns for cross-sells and up-sells.
add_filter( 'loop_shop_columns', 'mp_loop_columns' );
add_filter( 'woocommerce_cross_sells_columns', 'mp_loop_columns' );
function mp_loop_columns() {
	if ( is_cart() ) {
		return 4;
	}
	return 4;
}

// Print product excerpt, if we want that in the loop.
function mp_loop_product_excerpt() {
	global $post;
	$attrs = array(
		'class' => 'uk-text-meta uk-margin',
	);
	// Use first line of post content if short description ($post->post_excerpt) is empty.
	$post_excerpt      = empty( $post->post_excerpt ) ? preg_split( '#\r?\n#', ltrim( $post->post_content ), 0 )[0] : $post->post_excerpt;
	$post_excerpt_trim = wp_trim_words( $post_excerpt, 16 );
	if ( ! empty( $post_excerpt_trim ) ) {
		echo buildAttributes( $attrs, 'div', $post_excerpt_trim );
	}
}
