<?php
/**
 * WooCommerce Checkout Terms & Conditions, Privacy Policy, etc.
 *
 * @package woocommerce
 */

// Replace Terms and Conditions page link with modal on checkout page.
add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', 'mp_wc_get_terms_and_conditions_checkbox_text' );
function mp_wc_get_terms_and_conditions_checkbox_text( $text ) {
	if ( is_checkout() ) {
		$terms_and_conditions = __( 'terms and conditions', 'woocommerce' );
		$terms_link           = buildAttributes(
			array(
				'href' => '#modal-terms-of-service',
				'uk-toggle',
			),
			'a',
			esc_attr( $terms_and_conditions )
		);
		// $text                 = sprintf( __( 'I have read and agree to the website %s', 'woocommerce' ), $terms_link );
		$text = str_replace( '[terms]', $terms_link, $text );
	}
	return $text;
}

// Replace Privacy Policy page link with modal on checkout page.
add_filter( 'woocommerce_get_privacy_policy_text', 'mp_wc_get_privacy_policy_text' );
function mp_wc_get_privacy_policy_text( $text ) {
	if ( is_checkout() ) {
		$privacy_policy      = __( 'privacy policy', 'woocommerce' );
		$privacy_policy_link = buildAttributes(
			array(
				'href' => '#modal-privacy-policy',
				'uk-toggle',
			),
			'a',
			esc_attr( $privacy_policy )
		);
		$text                = str_replace( '[privacy_policy]', $privacy_policy_link, $text );
	}
	return $text;
}
