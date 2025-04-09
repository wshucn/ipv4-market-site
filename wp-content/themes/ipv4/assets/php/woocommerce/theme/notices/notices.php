<?php
/**
 * WooCommerce Notices
 *
 * @package woocommerce
 */

// Adds uk-alert styles to dynamically created form validation errors.
add_action( 'woocommerce_edit_account_form', 'mp_wc_form_error' );
add_action( 'woocommerce_after_checkout_form', 'mp_wc_form_error' );
add_action( 'woocommerce_add_payment_method_form_bottom', 'mp_wc_form_error' );
function mp_wc_form_error() {
	add_action( 'wp_footer', 'mp_wc_form_error_script' );
}
function mp_wc_form_error_script() {
	?>
<script type='text/javascript'>
	var observer = new MutationObserver(function(mutations) {
		mutations.forEach(function(mutation) {
			jQuery(mutation.addedNodes).each(function() {
				if (jQuery(this).hasClass('woocommerce-error')) {
					jQuery(this).addClass('uk-alert uk-alert-warning');
				}
			});
		});
	});
	jQuery('.woocommerce form').each(function() {
		observer.observe(this, {
			childList: true,
			subtree: true
		});
	});
</script>
	<?php
}


add_action( 'woocommerce_before_main_content', 'mp_wc_notices_script_enqueue', 20, 0 );
function mp_wc_notices_script_enqueue() {
	add_action( 'wp_footer', 'mp_wc_notices_script' );
}
function mp_wc_notices_script() {
	?>
	<script type='text/javascript'>
		jQuery(window).on('load', function(){
			// WooCommerce success messages (such as '... has been added to your cart.') show as UIkit alerts
			var $woo_message = jQuery('.woocommerce-message.uk-alert-success');
			$woo_message.each(function(){
				var $message_html = jQuery(this).html();
				UIkit.notification($message_html, {status:'success', timeout: 1000000});
			});

		});
	</script>
	<?php
}
