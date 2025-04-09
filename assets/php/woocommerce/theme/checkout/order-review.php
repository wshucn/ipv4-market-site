<?php
/**
 * WooCommerce Checkout Order Review Table
 *
 * @package woocommerce
 */

// Move Coupon form from woocommerce_before_checkout_form to woocommerce_review_order_after_cart_contents.
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );
// add_action( 'woocommerce_review_order_after_cart_contents', 'woocommerce_checkout_coupon_form' );


add_filter( 'woocommerce_update_order_review_fragments', 'mp_wc_checkout_order_summary_total_update' );
function mp_wc_checkout_order_summary_total_update( $fragments ) {
	ob_start();
	?>
	<div class='woocommerce-checkout-order-summary-total order-total woocommerce-price-hide-currency'>
		<span role='heading' class='screen-reader-text'><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
		<span><?php wc_cart_totals_order_total_html(); ?></span>
	</div>
	<?php
	$woocommerce_checkout_order_summary_total               = ob_get_clean();
	$fragments['.woocommerce-checkout-order-summary-total'] = $woocommerce_checkout_order_summary_total;
	return $fragments;
}


add_action( 'woocommerce_checkout_after_order_review', 'mp_wc_checkout_order_review_toggle_script_add' );
function mp_wc_checkout_order_review_toggle_script_add() {
	add_action( 'wp_footer', 'mp_wc_checkout_order_review_toggle_script' );
}
function mp_wc_checkout_order_review_toggle_script() {
	?>
	<script type='text/javascript'>
		jQuery('.order_review_toggle').hide();
		jQuery('.order_review_toggle').on('show', function(e){
			jQuery('#order_review_toggle').find('span').text('<?php esc_html_e( 'Hide order summary', 'woocommerce' ); ?>');
			jQuery('#order_review_toggle').find('.toggle-icon').attr('name', 'chevron-up');
			jQuery('#woocommerce_checkout_order_review').slideDown();
		});
		jQuery('.order_review_toggle').on('hide', function(e){
			jQuery('#order_review_toggle').find('span').text('<?php esc_html_e( 'Show order summary', 'woocommerce' ); ?>');
			jQuery('#order_review_toggle').find('.toggle-icon').attr('name', 'chevron-down');
			jQuery('#woocommerce_checkout_order_review').slideUp();
		});
	</script>
	<?php
}


// Clone the Coupon Code form so that we can have it in two places in the checkout form.
// Using the usual function removes the <form> tag.
add_action( 'woocommerce_review_order_before_payment', 'mp_wc_checkout_coupon_clone_form' );
function mp_wc_checkout_coupon_clone_form() {
	echo "<div role='table' class='uk-hidden@m' id='clone-coupon-code'></div>";
	add_action( 'wp_footer', 'mp_wc_clone_coupon_code_script' );
}
function mp_wc_clone_coupon_code_script() {
	?>
	<script type='text/javascript'>
		var count = 0;

		jQuery( 'body' ).on( 'updated_checkout', function() {
			const coupon_code = document.getElementById('apply-coupon-form');

			var source = jQuery(coupon_code),
				clone = source.clone();

			clone.find(':input').attr('id', function(i, val) { return val + count; });
			clone.find(':submit').attr('id', function(i, val) { return val + count; });

			const clone_coupon_code = jQuery('#clone-coupon-code');
			clone_coupon_code.html(clone);

			count++;
		});
	</script>
	<?php
}
