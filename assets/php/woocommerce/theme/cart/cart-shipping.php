<?php
/**
 * WooCommerce Cart/Checkout Shipping
 *
 * @package woocommerce
 */

// Shipping method label in the cart order table.
add_filter( 'woocommerce_cart_shipping_method_full_label', 'mp_wc_cart_shipping_method_full_label', 20, 2 );
function mp_wc_cart_shipping_method_full_label( $full_label, $method ) {
	// Remove the colon.
	$label      = $method->get_label();
	$full_label = str_replace( $label . ':', $label, $full_label );

	// Float the price right.
	$full_label = str_replace( 'woocommerce-Price-amount amount', 'woocommerce-Price-amount amount uk-text-bolder uk-float-right', $full_label );
	return $full_label;
}

/**
 * Get a shipping method price. This ensures the price is determined by WooCommerce's own processes and includes tax (if applicable).
 *
 * @param  WC_Shipping_Rate $method Shipping method rate data.
 * @return string
 */
function mp_wc_cart_totals_shipping_method_cost( $method ) {
	$label = wc_cart_totals_shipping_method_label( $method );

	$dom     = mp_load_html( $label );
	$xpath   = new DomXPath( $dom );
	$nodes   = $xpath->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' woocommerce-Price-amount ')]" );
	$tmp_dom = new DOMDocument();
	foreach ( $nodes as $node ) {
		$tmp_dom->appendChild( $tmp_dom->importNode( $node, true ) );
	}
	$cost = mp_save_html( $tmp_dom );
	$cost = mp_html_class_by_class( $cost, 'woocommerce-Price-amount', 'woocommerce-Price-amount amount' );

	return apply_filters( 'woocommerce_cart_shipping_method_cost', $cost, $method );
}


// Shipping name/label in the cart order table.
// add_filter( 'woocommerce_shipping_package_name', 'mp_wc_shipping_package_name_default', 10, 3 );
function mp_wc_shipping_package_name_default( $name, $i, $package ) {
	// if ( __( 'Shipping', 'woocommerce' ) === $name ) {
	// $available_methods = $package['rates'];
	// $chosen_method     = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';

	// if ( $available_methods ) {
	// $shipping_description = esc_html__( , 'woocommerce' );
	// }

	// if ( 0 === $index ) {
	// $name = __( 'Shipping', 'woocommerce' );
	// }

	// $shipping_row_header = empty( $available_methods[ $chosen_method ]->label ) ? esc_html__( 'Shipping', 'woocommerce' ) : $available_methods[ $chosen_method ]->label;
	// echo wp_kses_post( sprintf( '%s: <span class="uk-text-muted">%s</span>', $package_name, $shipping_row_header ) );
	return $name;
}


// Alteration of native WC function to print the shipping totals only.
// We use a different template, 'cart/cart-totals-shipping.php' rather than 'cart/cart-shipping.php'.
// For use in the Order Review table.
// add_filter( 'wc_get_template', 'mp_wc_cart_shipping_template', 10, 1 );
// function mp_wc_cart_shipping_template( $template ) {
// if ( strpos( $template, 'cart/cart-shipping.php' ) !== false ) {
// return str_replace( 'cart/cart-shipping.php', 'cart/cart-totals-shipping.php', $template );
// }
// return $template;
// }

function mp_wc_cart_totals_shipping_html() {
	$packages = WC()->shipping()->get_packages();
	$first    = true;

	foreach ( $packages as $i => $package ) {
		$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
		$product_names = array();

		if ( count( $packages ) > 1 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
			}
			$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );
		}

		wc_get_template(
			'cart/cart-totals-shipping.php',
			array(
				'package'                  => $package,
				'available_methods'        => $package['rates'],
				'show_package_details'     => count( $packages ) > 1,
				'show_shipping_calculator' => is_cart() && apply_filters( 'woocommerce_shipping_show_shipping_calculator', $first, $i, $package ),
				'package_details'          => implode( ', ', $product_names ),
				/* translators: %d: shipping package number */
				'package_name'             => apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'woocommerce' ), $i, $package ),
				'package_label'            => apply_filters( 'woocommerce_shipping_package_label', ( WC()->customer->has_calculated_shipping() && ! empty( $chosen_method ) && ! empty( $package['rates'] ) ) ? $package['rates'][ $chosen_method ]->label : '', $i, $package ),
				'index'                    => $i,
				'chosen_method'            => $chosen_method,
				'formatted_destination'    => WC()->countries->get_formatted_address( $package['destination'], ', ' ),
				'has_calculated_shipping'  => WC()->customer->has_calculated_shipping(),
			)
		);

		$first = false;
	}
}



// Check if any cart item contains a certain shipping class.
function cart_has_shipping_class( $class, $cart = array() ) {
	if ( empty( $cart ) ) {
		$cart = WC()->cart->get_cart();
	}
	foreach ( $cart as $cart_item ) {
		if ( $class === $cart_item['data']->get_shipping_class() ) {
			return true;
		}
	}
	return false;
}

// Check if all cart items contain a certain shipping class.
function cart_has_only_shipping_class( $class, $cart = array() ) {
	if ( empty( $cart ) ) {
		$cart = WC()->cart->get_cart();
	}
	foreach ( $cart as $cart_item ) {
		if ( $class !== $cart_item['data']->get_shipping_class() ) {
			return false;
		}
	}
	return true;
}

/**
 * Returns true if shipping method for all packages is free shipping (so we don't need an address to calculate)
 *
 * @return boolean
 */
function is_free_shipping() {
	if ( ! is_checkout() ) {
		return false;
	}

	$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
	if ( is_array( $chosen_shipping_methods ) ) {
		$chosen_shipping_methods_unique = array_unique( $chosen_shipping_methods );
		if ( mp_array_starts_with( $chosen_shipping_methods_unique, 'free_shipping' ) ) {
			return true;
		}
	}
	return false;
}


