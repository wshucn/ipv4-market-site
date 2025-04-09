<?php
/**
 * WooCommerce Checkout Shipping
 *
 * @package woocommerce
 */

// Fragment to update the Checkout shipping table, which is no longer part of the Order Review table.
// See function mp_wc_cart_totals_shipping_html().
add_filter( 'woocommerce_update_order_review_fragments', 'mp_wc_checkout_shipping_table_update' );
function mp_wc_checkout_shipping_table_update( $fragments ) {
	ob_start();
	?>
	<div class='woocommerce-checkout-shipping'>
		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
		<?php endif; ?>
	</div>
	<?php
	$woocommerce_shipping_methods                = ob_get_clean();
	$fragments['.woocommerce-checkout-shipping'] = $woocommerce_shipping_methods;
	return $fragments;
}
