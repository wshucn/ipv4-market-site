<?php
/**
 * WooCommerce Add-to-Cart button
 *
 * @package woocommerce
 */

// To change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' );
function woocommerce_custom_single_add_to_cart_text() {
	 return __( 'Add to Cart', 'woocommerce' );
}


// To change add to cart text on product archives(Collection) page
add_filter( 'woocommerce_product_add_to_cart_text', 'mp_product_add_to_cart_text', 10, 2 );
function mp_product_add_to_cart_text( $text, $product ) {
	// $loop_name = esc_attr(wc_get_loop_prop('name'));
	if ( $product->is_type( 'variable' ) ) {
		$text = __( 'Choose Options', 'woocommerce' );
	} else {
		$text = __( 'Add to Cart', 'woocommerce' );
	}
	return $text;
}

add_filter(
	'woocommerce_product_add_to_cart_description',
	function ( $sprintf, $product ) {
		return sprintf( 'Add %s to cart', $product->get_name() );
	},
	10,
	2
);

/*
 Create Buy Now Button dynamically after Add To Cart button */
// add_action('woocommerce_after_add_to_cart_button', 'mp_after_add_to_cart_button_buynow');
function mp_after_add_to_cart_button_buynow() {
	global $product;
	$cart_url = $product->add_to_cart_url();
	if ( empty( $cart_url ) ) {
		return false;
	}

	// parse query into array
	parse_str( trim( $cart_url, '?' ), $cart_query );

	// get the "Checkout Page" URL
	$checkout_url = wc_get_checkout_url();
	// if(str_contains($checkout_url, '?page_id')) $cart_url = str_replace('?', '&', $cart_url);
	echo buildAttributes(
		array(
			'href'  => add_query_arg( $cart_query, $checkout_url ),
			'name'  => 'buy-now',
			'class' => 'uk-width-1-1 uk-width-small@s uk-button uk-button-secondary uk-text-nowrap single_buy_now_button',
		),
		'a',
		__( 'Buy Now', 'woocommerce' )
	);

	add_action( 'wp_footer', 'mp_wc_quantity_update_buynow_script' );

}
