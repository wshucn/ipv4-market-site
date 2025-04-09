<?php
/**
 * WooCommerce Cart/Checkout - FFL Fields
 * Client-specific
 *
 * @package woocommerce
 */

// Add a notice when an order contains FFL items
add_action( 'woocommerce_before_cart', 'maybe_add_ffl_notice' );
function maybe_add_ffl_notice( $cart ) {

	// $cart = WC()->cart;
	// Check cart items for specific shipping class, displaying a notice.
	if ( cart_has_shipping_class( 'ffl' ) ) {
		// $cart->add_fee(__('FFL Flat Shipping Fee', 'woocommerce'), 10, true);
		mp_write_log( 'Cart contains FFL items.' );
		// wc_clear_notices();
		wc_print_notice(
			__( 'Your cart contains items that must be collected through an authorized FFL dealer. You must provide the dealer address at checkout.', 'woocommerce' ),
			'notice'
		);
	}
}

// Add FFL field to Checkout fields.
add_filter( 'woocommerce_checkout_fields', 'mp_wc_ffl_checkout_fields' );
function mp_wc_ffl_checkout_fields( $fields ) {
	if ( ! cart_has_shipping_class( 'ffl' ) ) {
		return $fields;
	}

	$field_key   = 'ffl';
	$field_label = 'FFL';

	$fields[ $field_key ] = array(
		$field_key . '_company'   => array(
			'type'        => 'text',
			'label'       => __( $field_label . ' Business Name', 'woocommerce' ),
			'placeholder' => _x( $field_label . ' Business Name', 'placeholder', 'woocommerce' ),
			'required'    => true,
			'class'       => array( 'form-row form-row-wide' ),
			'priority'    => 200,
		),
		$field_key . '_address_1' => array(
			'type'        => 'text',
			'label'       => __( $field_label . ' Address', 'woocommerce' ),
			'placeholder' => _x( $field_label . ' Address', 'placeholder', 'woocommerce' ),
			'required'    => true,
			'class'       => array( 'form-row', 'form-row-wide', 'address-field' ),
			'priority'    => 210,
		),
		$field_key . '_address_2' => array(
			'type'        => 'text',
			'label'       => __( $field_label . ' Apartment, suite, etc.', 'woocommerce' ),
			'placeholder' => _x( $field_label . ' Apartment, suite, etc.', 'placeholder', 'woocommerce' ),
			'required'    => false,
			'class'       => array( 'form-row', 'form-row-wide', 'address-field' ),
			'priority'    => 220,
		),
		$field_key . '_city'      => array(
			'type'        => 'text',
			'label'       => __( $field_label . ' City', 'woocommerce' ),
			'placeholder' => _x( $field_label . ' City', 'placeholder', 'woocommerce' ),
			'required'    => true,
			'class'       => array( 'form-row', 'address-field', 'uk-width-expand@s' ),
			'priority'    => 230,
		),
		$field_key . '_state'     => array(
			'type'        => 'state',
			'country'     => 'US',
			'label'       => __( $field_label . ' State', 'woocommerce' ),
			'placeholder' => _x( $field_label . ' State', 'placeholder', 'woocommerce' ),
			'required'    => true,
			'class'       => array( 'form-row', 'address-field', 'uk-width-expand@s' ),
			'priority'    => 240,
		),
		$field_key . '_postcode'  => array(
			'type'        => 'text',
			'label'       => __( $field_label . ' Postcode', 'woocommerce' ),
			'placeholder' => _x( $field_label . ' Postcode', 'placeholder', 'woocommerce' ),
			'required'    => true,
			'class'       => array( 'form-row', 'address-field', 'uk-width-expand@s' ),
			'priority'    => 250,
		),
		// $field_key . '_country'   => array(
		// 'type'        => 'hidden',
		// 'label'       => __( $field_label . ' Country', 'woocommerce' ),
		// 'placeholder' => _x( $field_label . ' Country', 'placeholder', 'woocommerce' ),
		// 'required'    => true,
		// 'class'       => array( 'form-row', 'address-field', 'uk-hidden' ),
		// 'label_class' => array( 'uk-hidden' ),
		// 'priority'    => 260,
		// ),
	);

	return $fields;
}

// Display FFL fields on checkout page.
add_action( 'woocommerce_checkout_after_customer_details', 'mp_wc_ffl_checkout_fields_display' );
function mp_wc_ffl_checkout_fields_display() {
	$field_key = 'ffl';

	if ( cart_has_shipping_class( $field_key ) ) {
		$checkout = WC()->checkout();

		$values = (array) WC()->session->get( $field_key );
		if ( empty( $values ) ) {
			$values = (array) WC()->customer->get_meta( $field_key );
		}

		// Set default country value to US.
		// $values[ $field_key . '_country' ] = 'US';

		// Get the names of the FFL products to display above the form. Shipping options form also displays them.
		// But if there is only one shipping option, the shipping form is hidden and so is the package contents.
		$packages = WC()->shipping()->get_packages();

		foreach ( $packages as $i => $package ) {

			if ( ! cart_has_only_shipping_class( 'ffl', $package['contents'] ) ) {
				continue;
			}
			$product_names = array();

			if ( count( $packages ) > 1 ) {
				foreach ( $package['contents'] as $item_id => $values ) {
					$product_names[ $item_id ] = $values['data']->get_name();
				}
				$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );
			}
		}

		?>

		<div id='ffl_details'>
			<h3 class='alt'><?php echo wp_kses_post( 'FFL shipping details' ); ?></h3>
			<p><?php echo wp_kses_post( 'Your cart contains at least one item that must be collected through an authorized FFL dealer. You must provide the dealer address.', 'woocommerce' ); ?></p>
			<ul class='woocommerce-shipping-contents uk-text-meta uk-list uk-list-disc'>
				<?php echo wp_kses_post( implode( '', list_items( $product_names ) ) ); ?>
			</ul>
			<div class='woocommerce-ffl-fields__field-wrapper uk-grid uk-grid-small' uk-grid uk-margin>
				<?php
				foreach ( $checkout->checkout_fields['ffl'] as $key => $field ) :
					$value = isset( $values[ $key ] ) ? $values[ $key ] : '';
					?>
					<?php woocommerce_form_field( $key, $field, $value ); ?>
				<?php endforeach; ?>
			</div>
		</div>

		<?php
	}
}

// Save the FFL field when checkout is processed.
add_action( 'woocommerce_checkout_update_order_meta', 'mp_wc_ffl_checkout_fields_save', 10, 2 );
function mp_wc_ffl_checkout_fields_save( $order_id, $data ) {
	$field_key = 'ffl';
	$country   = 'US';

	$address_fields = WC()->countries->get_address_fields( $country, '' );

	foreach ( array_keys( $address_fields ) as $address_field ) {
		$address_field_key = $field_key . '_' . $address_field;
		// mp_write_log( 'Checking for posted ' . $address_field_key );
		if ( isset( $data[ $address_field_key ] ) ) {
			// mp_write_log( 'Updating order record with ' . $address_field_key );
			update_post_meta( $order_id, $address_field_key, sanitize_text_field( $data[ $address_field_key ] ) );
		}
	}
	// Set the country (because FFL will always be US).
	update_post_meta( $order_id, $field_key . '_country', 'US' );
}

// Display the FFL field in the order admin panel.
add_action( 'woocommerce_admin_order_data_after_order_details', 'mp_wc_ffl_checkout_fields_admin' );
function mp_wc_ffl_checkout_fields_admin( $order ) {
	$field_key     = 'ffl';
	$order_id      = $order->get_id();
	$order_address = array();

	$address_fields = WC()->countries->get_address_fields( 'US', '' );

	foreach ( array_keys( $address_fields ) as $address_field ) {
		$order_address_field = get_post_meta( $order_id, $field_key . '_' . $address_field, true );

		if ( ! empty( $order_address_field ) ) {
			$order_address[ $address_field ] = $order_address_field;
		}
	}

	if ( empty( $order_address['postcode'] ) ) {
		return;
	}
	?>
	<br class='clear' />
	<h3><?php esc_html_e( 'FFL Shipping Address', 'woocommerce' ); ?></h3>
	<div class='address'><p>
		<?php echo wp_kses_post( WC()->countries->get_formatted_address( $order_address ) ); ?>
	</p></div>
	<?php
}


/**
 * Split all shipping class 'ffl' products in a separate package
 */
function mp_wc_split_shipping_packages_shipping_class( $packages ) {

	// Reset all packages.
	$packages              = array();
	$regular_package_items = array();
	$split_package_items   = array();

	$split_shipping_class = 'ffl'; // Shipping class slug.
	$field_key            = 'ffl'; // Destination address custom field prefix.

	foreach ( WC()->cart->get_cart() as $item_key => $item ) {

		if ( $item['data']->needs_shipping() ) {

			if ( $split_shipping_class === $item['data']->get_shipping_class() ) {
				$split_package_items[ $item_key ] = $item;
			} else {
				$regular_package_items[ $item_key ] = $item;
			}
		}
	}

	// Create shipping packages.
	if ( ! empty( $regular_package_items ) ) {
		$packages[] = array(
			'contents'        => $regular_package_items,
			'contents_cost'   => array_sum( wp_list_pluck( $regular_package_items, 'line_total' ) ),
			'applied_coupons' => WC()->cart->get_applied_coupons(),
			'user'            => array(
				'ID' => get_current_user_id(),
			),
			'destination'     => array(
				'country'   => WC()->customer->get_shipping_country(),
				'state'     => WC()->customer->get_shipping_state(),
				'postcode'  => WC()->customer->get_shipping_postcode(),
				'city'      => WC()->customer->get_shipping_city(),
				'address_1' => WC()->customer->get_shipping_address(),
				'address_2' => WC()->customer->get_shipping_address_2(),
			),
		);
	}

	if ( ! empty( $split_package_items ) ) {

		$values = (array) WC()->session->get( $field_key );
		if ( empty( $values ) ) {
			$values = (array) WC()->customer->get_meta( $field_key );
		}

		$address_fields = WC()->countries->get_address_fields( 'US', '' );

		$destination            = array_fill_keys( array_keys( $address_fields ), '' );
		$destination['country'] = 'US';

		foreach ( array_keys( $address_fields ) as $address_field ) {
			if ( isset( $values[ $field_key . '_' . $address_field ] ) ) {
				$destination[ $address_field ] = $values[ $field_key . '_' . $address_field ];
			}
		}

		$packages[] = array(
			'contents'        => $split_package_items,
			'contents_cost'   => array_sum( wp_list_pluck( $split_package_items, 'line_total' ) ),
			'applied_coupons' => WC()->cart->get_applied_coupons(),
			'user'            => array(
				'ID' => get_current_user_id(),
			),

			'destination'     => $destination,
		);
	}

	return $packages;

}
add_filter( 'woocommerce_cart_shipping_packages', 'mp_wc_split_shipping_packages_shipping_class' );

// If only FFL items, cart does not need to collect customer shipping address.
add_filter( 'woocommerce_cart_needs_shipping_address', 'mp_wc_cart_needs_shipping_address' );
function mp_wc_cart_needs_shipping_address( $needs_shipping ) {
	if ( cart_has_only_shipping_class( 'ffl' ) ) {
		return false;
	}
	return $needs_shipping;
}


// By default, different shipping packages are named Shipping 2, Shipping 3.
// Since we're splitting the packages by shipping class, use a class-specific name instead.
function mp_wc_shipping_package_name( $name, $id, $package ) {

	// Bail if package error.
	if ( ! isset( $package['contents'] ) || ! is_array( $package['contents'] ) ) {
		return $name;
	}

	if ( cart_has_only_shipping_class( 'ffl', $package['contents'] ) ) {
		return __( 'Shipping to FFL', 'woocommerce' );
	}

	return $name;

}
add_filter( 'woocommerce_shipping_package_name', 'mp_wc_shipping_package_name', 10, 3 );


/**
 * Shorten the shipping class name for the totals table
 *
 * @return string
 */
function mp_wc_cart_totals_shipping_package_name( $package_name, $package ) {
	$field_key = 'ffl';
	// $ffl_shipping_class = get_term_by( 'slug', 'ffl', 'product_shipping_class' );
	// if ( $package_name === $ffl_shipping_class->name ) {
	// $package_name = __( 'FFL Transfer', 'woocommerce' );
	// } else {
	// $package_name = __( 'Shipping', 'woocommerce' );
	// }
	if ( cart_has_shipping_class( $field_key, $package['contents'] ) ) {
		$package_name = __( 'FFL Transfer', 'woocommerce' );
	} else {
		$package_name = __( 'Shipping', 'woocommerce' );
	}

	return $package_name;
}
add_filter( 'woocommerce_cart_totals_shipping_package_name', 'mp_wc_cart_totals_shipping_package_name', 10, 2 );
// Try this hook to simplify things:
// add_filter( 'woocommerce_shipping_package_name', 'mp_wc_cart_totals_shipping_package_name', 10, 2 );




// Calculate shipping for FFL package based on FFL address fields.
// This function has been incorporated into 'mp_wc_split_shipping_packages_shipping_class' function,
// which splits the order into packages based on shipping class.
// add_filter( 'woocommerce_cart_shipping_packages', 'mp_wc_cart_shipping_packages' );
function mp_wc_cart_shipping_packages( $packages ) {
	$field_key = 'ffl';

	foreach ( $packages as &$package ) {
		if ( ! cart_has_shipping_class( $field_key, $package['contents'] ) ) {
			continue;
		}

		$values = (array) WC()->session->get( $field_key );
		if ( empty( $values ) ) {
			$values = (array) WC()->customer->get_meta( $field_key );
		}

		$address_fields            = WC()->countries->get_address_fields( 'US', 'shipping_' );
		$address_fields['country'] = 'US';

		foreach ( array_keys( $address_fields ) as $address_field ) {
			if ( isset( $values[ $field_key . $address_field ] ) ) {
				$package['destination'][ $address_field ] = $values[ $field_key . $address_field ];
			}
		}
	}
	return $packages;
}



// Enable only local pickup for FFL shipping class.
add_filter( 'woocommerce_package_rates', 'mp_wc_package_rates_limit_by_class', 10, 2 );
function mp_wc_package_rates_limit_by_class( $rates, $package ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	// HERE define the shipping methods to show
	// For UPS Shipping, its wf_shipping_ups:[service code], where the service codes can be found in the UPS shipping settings Rates & Services tab.
	$methods = array(
		'wf_shipping_ups:03',       // UPS Ground
	);

	// Checking in cart items
	if ( cart_has_shipping_class( 'ffl', $package['contents'] ) ) {
		// $rates = array_filter( $rates, fn( $k) => $k === $method_key_id, ARRAY_FILTER_USE_KEY );
		$rates = array_intersect_key( $rates, array_flip( $methods ) );
		// $rates = array();
	}

	// foreach ( $package['contents'] as $item ) {
	// If we find the shipping class
	// if ( $item['data']->get_shipping_class_id() === $class->term_id ) {
	// $rates = array_filter( $rates, fn( $k) => $k === $method_key_id, ARRAY_FILTER_USE_KEY );
	// $rates = array();
	// break; // Stop the loop
	// }
	// }
	return $rates;
}


// Ajax sender.
// Use this if it makes sense to use a custom sender, apart from the 'update_order_review' (checkout)
// or 'update_shipping_method' (cart), which already send the form data when an .address_field
// input changes. You will also then need to use the wc_ajax_[action] hook to deal with the data.

// checkout order review nonce: security: wc_checkout_params.update_order_review_nonce
// cart shipping nonce: security: wc_cart_params.update_shipping_method_nonce

// add_action( 'wp_footer', 'checkout_send_ffl_via_ajax_js' );
function checkout_send_ffl_via_ajax_js() {
	if ( is_checkout() && ! is_wc_endpoint_url() ) :
		?>
	<script type="text/javascript">
	jQuery( function($){
		if (typeof wc_checkout_params === 'undefined')
			return false;

		// Function that send the Ajax request
		function sendAjaxRequest( value ) {
			$.ajax({
				type: 'POST',
				url: wc_checkout_params.ajax_url,
				security: wc_checkout_params.update_order_review_nonce,
				data: {
					'action': 'ffl_update_address',
					'ffl': value
				},
				success: function ( response ) {
					$(document.body).trigger('update_checkout'); // Update checkout processes
				}
			});
		}

		// FFL change & input events
		$(document.body).on( 'change input', '#ffl_details input, #ffl_state', function() {
			sendAjaxRequest( $('#ffl_details input, #ffl_state').serialize() );
		});
	});
	</script>
		<?php
	endif;
}

// Monitor AJAX data being send and responded.
// add_action( 'wp_footer', 'monitor_jquery_ajax_requests' );
function monitor_jquery_ajax_requests() {
	?>
	<script>
	jQuery(document).ajaxSend( function( event, xhr, options ) {
		console.log('------- ' + event.type + ' -------');
		console.log(options);
		console.log('------------------------');
	}).ajaxComplete( function( event, xhr, options ) {
		console.log('----- ' + event.type + ' -----');
		console.log(options);
		console.log('----------------------------');
	});
	</script>
	<?php
}

add_action( 'woocommerce_checkout_update_order_review', 'mp_wc_checkout_set_package_destination_to_wc_session' );
function mp_wc_checkout_set_package_destination_to_wc_session( $post_data ) {
	// $post_data is already wp_unslash()'d and needs only wc_clean() before a field value is saved.

	mp_write_log( 'Maybe setting FFL address to session' );

	// Our field prefix.
	$field_key = 'ffl';

	parse_str( $post_data, $post_data_parsed );
	// mp_write_log( print_r( $post_data_parsed, true ) );

	// Reduce the data array to keys beginning with the field prefix.
	$fields = array_filter(
		$post_data_parsed,
		function( $v, $k ) use ( $field_key ) {
			return ( strpos( $k, $field_key ) === 0 );
		},
		ARRAY_FILTER_USE_BOTH
	);

	// Get values from customer record if not set.
	if ( empty( array_filter( $fields ) ) ) {
		$fields = (array) WC()->customer->get_meta( $field_key );
	}

	if ( ! empty( $fields ) ) {
		// mp_write_log( 'FFL fields: ' . print_r( $fields, true ) );

		// Set the session variable.
		WC()->session->set( $field_key, wc_clean( $fields ) );

		// Set the customer meta data.
		// mp_write_log( 'Updating customer metadata: ' . $field_key );
		WC()->customer->update_meta_data( $field_key, wc_clean( $fields ) );

		WC()->customer->save(); // called later on in the update_order_review() function

	}
}

// Tell WooCommerce that we can calculate shipping when enough FFL fields are valid.
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'mp_wc_cart_show_shipping' );
function mp_wc_cart_show_shipping( $show_shipping ) {
	$field_key = 'ffl';

	$fields = (array) WC()->session->get( $field_key );

	// Get values from customer record if not set.
	if ( empty( array_filter( $fields ) ) ) {
		$fields = (array) WC()->customer->get_meta( $field_key );
	}

	if ( ! empty( $fields ) ) {
		if ( isset( $fields['ffl_state'] ) && isset( $fields['ffl_postcode'] ) ) {
			return true;
		}
	}

	return $show_shipping;
}

// Update checkout fields 'ffl' values from custom WC_session variable.
add_filter( 'woocommerce_checkout_get_value', 'mp_wc_checkout_update_ffl_fields_values', 10, 2 );
function mp_wc_checkout_update_ffl_fields_values( $value, $input ) {
	$field_key = 'ffl';

	$values = (array) WC()->session->get( $field_key );
	// mp_write_log( 'FFL fields (session): ' . print_r( $values, true ) );
	if ( isset( $values[ $input ] ) ) {
		return $values[ $input ];
	}

	return $value;
}


// Remove custom WC_Session variable once order has been created (before thankyou)
add_action( 'woocommerce_checkout_order_created', 'mp_wc_checkout_order_created_unset_ffl' );
function mp_wc_checkout_order_created_unset_ffl() {
	$field_key = 'ffl';

	// mp_write_log( 'Unsetting session variables: ' . $field_key );
	WC()->session->__unset( $field_key );

}
