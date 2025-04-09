<?php
/**
 * WooCommerce Forms & Fields
 * Client-specific
 *
 * @package woocommerce
 */

// Add Job Title to user billing fields
// add_filter( 'woocommerce_billing_fields', 'mp_billing_fields_job_title', 10, 1 );
function mp_billing_fields_job_title( $fields ) {
	$current_user                = wp_get_current_user();
	$default_value               = $current_user->job_title;
	$fields['billing_job_title'] = array(
		'label'       => __( 'Job title', 'woocommerce' ),
		'placeholder' => _x( 'Job title', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'type'        => 'text',
		'priority'    => 35,
		'default'     => $default_value,
	);
	return $fields;
}

// Update the order meta with Job Title field value.
// add_action( 'woocommerce_checkout_update_order_meta', 'mp_billing_job_title_update_order_meta' );
function mp_billing_job_title_update_order_meta( $order_id ) {
	if ( ! empty( $_POST['billing_job_title'] ) ) {
		update_post_meta( $order_id, '_job_title', sanitize_text_field( $_POST['billing_job_title'] ) );
	}
}
