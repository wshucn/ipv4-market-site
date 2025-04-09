<?php
/**
 * WooCommerce Prices
 *
 * @package woocommerce
 */


// Style Prices in the loop and single product page
// Gets passed the .amount container, and also $instance so you can conditionally style variation prices, for instance
// NOTE: This will target the prices in variable $xxx - $xxx on the product page, but not the hyphen itself.
add_filter( 'woocommerce_get_price_html', 'mp_wc_get_price_html', 10, 2 );
function mp_wc_get_price_html( $html, $product ) {
	if ( 'product_variation' === $product->post_type ) {
		$class = apply_filters( 'mp_wc_get_price_html_variation', '' );
	} else {
		$class = apply_filters( 'mp_wc_get_price_html_simple', '' );
	}
	// we don't want to target the crossed-out sale price (<del>)
	if ( ! empty( $class ) ) {
		$html = mp_html_class( $html, '//*[contains(@class, "amount") and not(ancestor::del)]', $class, true );
	}
	return $html;
}


// add_filter( 'formatted_woocommerce_price', 'mp_superscript_wc_formatted_price', 10, 5 );
function mp_superscript_wc_formatted_price( $formatted_price, $price, $decimal_places, $decimal_separator, $thousand_separator ) {
	// Leave prices unchanged in Dashboard.
	if ( is_admin() ) {
		return $formatted_price;
	}

	// Format units, including thousands separator if necessary.
	$unit = number_format( intval( $price ), 0, $decimal_separator, $thousand_separator );
	// Format decimals, with leading zeros as necessary (e.g. for 2 decimals, 0 becomes 00, 3 becomes 03 etc).
	$decimal      = '';
	$num_decimals = wc_get_price_decimals();
	if ( $num_decimals ) {
		$decimal = sprintf( '<sup>%0' . $num_decimals . 'd</sup>', round( ( $price - intval( $price ) ) * 100 ) );
	}

	return $unit . $decimal;
}


// Add currency classname to price currency symbol <span>.
add_filter( 'wc_price', 'mp_wc_price', 10, 5 );
function mp_wc_price( $html, $price, $args, $unformatted_price, $original_price ) {
	$currency       = empty( $args['currency'] ) ? get_woocommerce_currency() : $args['currency'];
	$currency_label = buildAttributes(
		array(
			'class'  => 'woocommerce-Price-currencyLabel',
			'hidden' => true,
		),
		'abbr',
		$currency
	);
	$html           = mp_html_class_by_class( $html, 'woocommerce-Price-currencySymbol', $currency, true );
	$html           = mp_move_element( $html, $currency_label, '.woocommerce-Price-currencySymbol' );
	return $html;
}
