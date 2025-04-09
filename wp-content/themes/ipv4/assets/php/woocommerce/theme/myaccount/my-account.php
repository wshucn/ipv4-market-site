<?php
/**
 * WooCommerce My Account
 *
 * @package woocommerce
 */

// Rename the endpoint title to whatever you set in the My Account menu filter.
foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
	add_filter( 'woocommerce_endpoint_' . $endpoint . '_title', 'mp_wc_endpoint', 10, 2 );
}

function mp_wc_endpoint( $title, $endpoint ) {
	if ( is_admin() ) {
		return $title;
	}
	$endpoints = wc_get_account_menu_items();
	$title     = $endpoints[ $endpoint ];

	return $title;
}


// Redirect to Orders page when user logs in, unless it's during Checkout.
add_action( 'woocommerce_login_redirect', 'mp_woocommerce_login_redirect', 10, 2 );
function mp_woocommerce_login_redirect( $redirect, $user ) {
	$redirect_page_id = url_to_postid( $redirect );
	$checkout_page_id = wc_get_page_id( 'checkout' );

	if ( $redirect_page_id == $checkout_page_id ) {
		return $redirect;
	}

	return get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . 'orders/';
}



// Change the Password Strength Meter language
add_action( 'wp_enqueue_scripts', 'mp_password_messages' );
function mp_password_messages() {
	wp_localize_script(
		'wc-password-strength-meter',
		'pwsL10n',
		array(
			'short'    => 'Short',
			'bad'      => 'Bad',
			'good'     => 'Better',
			'strong'   => 'Strong',
			'mismatch' => 'Must match!',
		)
	);
}

add_filter( 'woocommerce_get_script_data', 'mp_strength_meter_custom_strings', 10, 2 );
function mp_strength_meter_custom_strings( $data, $handle ) {
	if ( 'wc-password-strength-meter' === $handle ) {
		$data_new = array(
			'i18n_password_error' => esc_attr__( 'weak password', 'theme-domain' ),
			'i18n_password_hint'  => esc_attr__( 'The password should be at least seven characters long.', 'theme-domain' ),
		);

		$data = array_merge( $data, $data_new );
	}

	return $data;
}



// Redirect after submitting password reset
add_action( 'woocommerce_customer_reset_password', 'mp_woocommerce_new_pass_redirect', 10, 1 );
function mp_woocommerce_new_pass_redirect( $user ) {
	wp_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ) );
	exit;
}
