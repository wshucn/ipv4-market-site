<?php
/**
 * WooCommerce Payment Table
 * Client-specific
 *
 * @package woocommerce
 */

add_action(
	'woocommerce_credit_card_form_start',
	function () {
		ob_start();
	},
	90
);
add_action(
	'woocommerce_credit_card_form_end',
	function () {

		$html  = '<div class="uk-grid uk-grid-small" uk-grid>';
		$html .= ob_get_clean();
		$html .= '</div>';

		// Make all form rows into <div>s.
		$html = mp_html_attrs( $html, '.form-row', array(), true, 'div' );

		// Display block on input wrappers.
		$html = mp_html_class_by_class( $html, 'woocommerce-input-wrapper', 'uk-display-block uk-form-controls', true );

		$html = mp_html_class_by_class( $html, 'form-row-first', 'uk-width-1-2@s', true );
		$html = mp_html_class_by_class( $html, 'form-row-last', 'uk-width-1-2@s', true );
		$html = mp_html_class_by_class( $html, 'form-row-wide', 'uk-width-1-1', true );

		$html = mp_html_class( $html, '//label[@for]', 'uk-form-label', true );

		$html = mp_html_class( $html, '//input[@type]', 'uk-input', true );

		$html = mp_html_attrs( $html, '//input[@autocomplete="cc-number"]', array( 'placeholder' => __( 'Card number', 'woocommerce' ) ), true );
		$html = mp_html_attrs( $html, '//input[@autocomplete="cc-exp"]', array( 'placeholder' => __( 'Expiration date (MM / YY)', 'woocommerce' ) ), true );
		$html = mp_html_attrs_by_class( $html, '.wc-credit-card-form-card-cvc', array( 'placeholder' => __( 'Security code', 'woocommerce' ) ), array( 'style' => false ) );

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	},
	90
);
