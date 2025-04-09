<?php
/**
 * WooCommerce Single Product page
 *
 * @package woocommerce
 */

add_filter( 'woocommerce_product_get_sku', 'mp_wc_product_get_sku', 10, 2 );
function mp_wc_product_get_sku( $value, $producct ) {
	if ( empty( $value ) ) {
		$value = __( 'N/A', 'woocommerce' );
	}
	return $value;
}

// Print the product categories.
function mp_wc_template_single_category() {
	global $product;
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wc_get_product_category_list(
		$product->get_id(),
		', ',
		'<p class="product_meta uk-text-bold uk-margin-remove"><span class="screen-reader-text posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' </span>',
		'</p>'
	);
}

// Print the product SKU.
function mp_wc_template_single_sku() {
	wc_get_template( 'single-product/sku.php' );
}
