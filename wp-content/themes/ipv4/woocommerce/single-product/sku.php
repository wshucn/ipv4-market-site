<?php
/**
 * Single Product SKU
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
<p class='product-meta uk-text-meta'><span class='sku_wrapper'>
	<abbr title='<?php esc_html_e( 'Part Number', 'woocommerce' ); ?>'><?php esc_html_e( 'P/N:', 'woocommerce' ); ?></abbr>
	<span class='sku'><?php echo wp_kses_post( $product->get_sku() ); ?></span></span>
</p>
	<?php
endif;
