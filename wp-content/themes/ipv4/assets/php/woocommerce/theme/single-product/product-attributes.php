<?php
/**
 * WooCommerce Product Attributes
 *
 * @package woocommerce
 */

// Don't check whether attribute is in product name.
add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );

