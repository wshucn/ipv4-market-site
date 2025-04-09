<?php
/**
 * WooCommerce filters and functions for our base e-commerce theme.
 */

$woocommerce_assets = array(
	// Theme stuff, generally static ...
	'theme'  => array(
		'cart/cart',
		'cart/cart-shipping',

		'checkout/order-review',
		'checkout/form-shipping',
		'checkout/terms',

		'emails/emails',

		'global/add-to-cart',
		'global/categories',
		'global/content',
		'global/form-fields',
		'global/gutenberg',
		'global/placeholder-img',
		'global/price',
		'global/quantity-input',
		'global/settings-pages',
		'global/store-notice',

		'loop/loop',
		'loop/orderby',

		'myaccount/my-account',

		'notices/notices',

		'order/order',

		'single-product/product-attributes',
		'single-product/product-image-zoom',
		'single-product/product-variations',
		'single-product/related',
		'single-product/review',
		'single-product/sale-flash',
		'single-product/single-product',
		'single-product/tabs',

		'vendor/media-cloud',
		// 'vendor/wcpao',
	),

	// Client-specific stuff, unique to client ...
	'client' => array(
		'categories',
		'form-fields',
		'loop',
		'my-account',
		'orderby',
		'price',
		'product-variations',
		'product-attributes',
		'store-notice',
		'payment',
		'ffl-order-field',
	),
);

foreach ( $woocommerce_assets as $woocommerce_asset_type => $woocommerce_asset_name ) {
	array_walk(
		$woocommerce_asset_name,
		function( $name, $key, $type ) {
			include_asset( "php/woocommerce/{$type}/{$name}.php" );
		},
		$woocommerce_asset_type
	);
}

// Disable WooCommerce default styles.
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

// Remove Woocommerce Select2.
add_action( 'wp_enqueue_scripts', 'woo_dequeue_select2', 100 );
function woo_dequeue_select2() {
	if ( class_exists( 'woocommerce' ) ) {
		wp_dequeue_style( 'select2' );
		wp_deregister_style( 'select2' );

		wp_dequeue_script( 'selectWoo' );
		wp_deregister_script( 'selectWoo' );
	}
}





