<?php
/**
 * WooCommerce Prices
 * Client-specific
 *
 * @package woocommerce
 */

// Trim zeros in price decimals.
// add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

// Class for .price container
add_filter(
	'woocommerce_product_price_class',
	fn( $class ) => buildClass(
		$class,
		'uk-text-secondary-lighter'
	)
);

// Style Prices in the loop and single product page.
// NOTE: This will target the prices in variable $xxx - $xxx on the product page, but not the hyphen itself.
add_filter(
	'mp_wc_get_price_html_variation',
	fn( $class ) => buildClass(
		$class,
		'uk-text-normal uk-text-primary'
	)
);

add_filter(
	'mp_wc_get_price_html_simple',
	fn( $class ) => buildClass(
		$class,
		'uk-text-bold uk-text-secondary'
	)
);

