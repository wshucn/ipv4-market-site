<?php
/**
 * WooCommerce Store Notice
 *
 * @package woocommerce
 */

// Don't show the Demo Store notice even if it is enabled. Add it into the page template to more
// precisely position it at the top of the page.
remove_action( 'wp_footer', 'woocommerce_demo_store' );
add_filter( 'woocommerce_demo_store', 'mp_woocommerce_demo_store', 10, 2 );
function mp_woocommerce_demo_store( $html, $notice ) {
	// <p class="woocommerce-store-notice demo_store" data-notice-id="...." style="display: none">
	// <?= wp_kses_post($notice) ? >
	// <a href="#" class="woocommerce-store-notice__dismiss-link">Dismiss</a>
	// </p>
	// Replace 'Dismiss' link with our own close button.
	$html = mp_html_remove_by_class( $html, 'woocommerce-store-notice__dismiss-link' );

	// Change the <p> to a <div>
	$html = mp_html_attrs_by_class( $html, 'woocommerce-store-notice', array(), false, 'div' );

	// Use a filter for classes so we can keep this file clean
	$class = apply_filters( 'mp_wc_demo_store_class', 'uk-section uk-section-xsmall' );

	// Wrap the notice
	ob_start();
	?>
<div style='display: none' class='woocommerce-store-notice demo_store <?php echo esc_attr( $class ); ?>'>
	<div class='uk-container uk-container-expand'>
		<div class='uk-flex uk-flex-between uk-flex-top'>
			<?php echo $html; ?>
			<button class="woocommerce-store-notice__dismiss-link uk-preserve-width uk-margin-medium-left" type="button" uk-close></button>
		</div>
	</div>
</div>
	<?php
	$html = ob_get_clean();
	return $html;
}
