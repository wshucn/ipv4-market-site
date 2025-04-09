<?php
/**
 * WooCommerce Sales Flash
 *
 * @package woocommerce
 */

// Loop and Single Product
add_filter(
	'woocommerce_sale_flash',
	function( $html ) {
		global $woocommerce_loop;

		if ( 'related' === $woocommerce_loop['name'] ) {
			$html = mp_html_class(
				$html,
				'.onsale',
				'uk-badge uk-card-badge uk-position-small',
				true
			);
		} elseif ( is_product() ) {
			$html = mp_html_class(
				$html,
				'.onsale',
				'uk-badge',
				true
			);
		} else {
			$html = mp_html_class(
				$html,
				'.onsale',
				'uk-badge uk-card-badge uk-position-small',
				true
			);
		}

		return $html;

	}
);
