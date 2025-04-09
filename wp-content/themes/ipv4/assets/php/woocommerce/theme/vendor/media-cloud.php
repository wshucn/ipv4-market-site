<?php
/**
 * WooCommerce / Media Cloud
 *
 * @package woocommerce
 */

// Disable WooCommerce generating thumbnails when imgix is enabled.
if ( true === get_option( 'mcloud-tool-enabled-imgix' ) ) {
	// Setting this to false will also remove the tool from the Tools tab.
	add_filter( 'woocommerce_background_image_regeneration', '__return_false' );
}
