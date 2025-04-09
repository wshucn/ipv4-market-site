<?php
/**
 * WooCommerce Product Add-ons
 *
 * @package woocommerce
 */

if ( class_exists( 'WC_Product_Addons' ) ) {
	// ob_start();
	// ? >
	// <style class='text/css'>

	// </style>
	// < ?php
	// $style = strip_tags( ob_get_clean() );
	// add_action('wp_enqueue_scripts', function() use($style) { wp_add_inline_style( 'mp-css', $style ); }, 950);


	// The Product Add-ons output has no hook (shame). We can capture and change it, however.
	add_action(
		'woocommerce_product_addons_start',
		function () {
			ob_start();
		},
		90
	);
	add_action(
		'woocommerce_product_addons_end',
		function () {
			$html = ob_get_clean();
			// Totals row needs to be full-width always.
			$html = mp_html_class( $html, 'product-addons-total', 'uk-width-1-1', true );
			// Wrap the field containers in a grid.
			echo buildAttributes(
				array(
					'class' => 'uk-grid uk-grid-medium',
					'uk-grid',
				),
				'div',
				$html
			);
		},
		90
	);
}
