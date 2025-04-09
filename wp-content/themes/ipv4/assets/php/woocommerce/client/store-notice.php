<?php
/**
 * WooCommerce Store Notice
 * Client-specific
 *
 * @package woocommerce
 */

add_filter(
	'mp_wc_demo_store_class',
	fn( $class ) => buildClass( $class, 'uk-section-secondary' )
);
