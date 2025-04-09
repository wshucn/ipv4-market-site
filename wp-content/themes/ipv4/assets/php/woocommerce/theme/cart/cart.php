<?php
/**
 * WooCommerce Cart
 *
 * @package woocommerce
 */

/**
 * Show cart contents / total AJAX
 * Since this needs to match the cart item HTML in your menu, we use a template for it.
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'mp_add_to_cart_fragments' );
function mp_add_to_cart_fragments( $fragments ) {
	ob_start();
	try {
		get_template_part( 'partials/header', 'nav-cart' );
		$fragments['a.menu-item-cart'] = ob_get_contents();
	} finally {
		ob_end_clean();
	}
	return $fragments;
}


// Change 'product removed' message
add_filter( 'woocommerce_cart_item_removed_title', 'mp_removed_from_cart_title', 12, 2 );
function mp_removed_from_cart_title( $message, $cart_item ) {
	$product = wc_get_product( $cart_item['product_id'] );

	if ( $product ) {
		$message = sprintf( __( '%s has been' ), $product->get_name() );
	}

	return $message;
}

// Change 'undo' button text; use this filter to change other text that cannot otherwise be got at.
add_filter( 'gettext', 'mp_text_translation', 35, 3 );
function mp_text_translation( $translation, $text, $domain ) {
	if ( 'Undo?' === $text && 'woocommerce' === $domain ) {
		$translation = __( 'Undo', $domain );
	}

	return $translation;
}



// Edit "has been added to your cart" message.
add_filter( 'wc_add_to_cart_message_html', 'mp_add_to_cart_message_html', 10, 3 );
function mp_add_to_cart_message_html( $message, $products ) {
	$titles = array();
	$count  = 0;

	foreach ( $products as $product_id => $qty ) {
		/* translators: %s: product name */
		$titles[] = apply_filters( 'woocommerce_add_to_cart_qty_html', absint( $qty ) . '&times; ', $product_id ) . apply_filters( 'woocommerce_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), strip_tags( get_the_title( $product_id ) ) ), $product_id );
		$count   += $qty;
	}

	$titles = array_filter( $titles );
	/* translators: %s: product name */
	$added_text = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', $count, 'woocommerce' ), wc_format_list_of_items( $titles ) );

	// Output success messages.
	if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
		$return_to = apply_filters( 'woocommerce_continue_shopping_redirect', wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false ) : wc_get_page_permalink( 'shop' ) );
		$message   = sprintf( '<div class="uk-margin-small-right">%s</div><div><a href="%s" tabindex="1" class="wc-forward">%s</a></div>', esc_html( $added_text ), esc_url( $return_to ), esc_html__( 'Continue shopping', 'woocommerce' ) );
	} else {
		$message = sprintf( '<div class="uk-margin-small-right">%s</div><div><a href="%s" tabindex="1" class="wc-forward">%s</a></div>', esc_html( $added_text ), esc_url( wc_get_cart_url() ), esc_html__( 'View cart', 'woocommerce' ) );
	}

	return $message;
}


// Returns the cart item's parent product. Useful for getting the parent thumbnail, for instance.
function cart_item_parent( $cart_item, $cart_item_key ) {
	$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	return wc_get_product( $_product->get_parent_id() );
}

// Use the parent product name for variations, so in showing the variation below we don't duplicate.
add_filter( 'woocommerce_cart_item_name', 'mp_wc_cart_item_name', 10, 3 );
function mp_wc_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
	if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
		$parent_product = cart_item_parent( $cart_item, $cart_item_key );
		$product_name   = $parent_product->get_name();
	}
	return $product_name;
}

// Use the variation product parent thumbnail instead of the variation thumbnail in cart and checkout.
add_filter( 'woocommerce_cart_item_thumbnail', 'mp_wc_checkout_cart_item_thumbnail', 10, 3 );
add_filter( 'woocommerce_checkout_cart_item_thumbnail', 'mp_wc_checkout_cart_item_thumbnail', 10, 3 );
function mp_wc_checkout_cart_item_thumbnail( $image_html, $cart_item, $cart_item_key ) {
	if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
		$parent_product = cart_item_parent( $cart_item, $cart_item_key );
		$image_html     = $parent_product->get_image(
			'thumbnail',
			array(
				'class' => 'uk-position-center',
			)
		);
	}

	$image_wrapper_attrs = array(
		'class' => array( 'uk-form-border', 'uk-inline', 'uk-background-white', 'uk-overflow-hidden' ),
		'style' => array( 'aspect-ratio: 1/1' ),
	);
	if ( is_checkout() ) {
		$image_wrapper_attrs['style'][] = 'width: 60px; height: 60px';
	} else {
		$image_wrapper_attrs['class'][] = 'uk-width-small';
	}

	$image_html = buildAttributes( $image_wrapper_attrs, 'span', $image_html );
	return $image_html;
}


// Style Cart Order table, Total price.
add_filter( 'woocommerce_cart_totals_order_total_html', 'mp_wc_cart_totals_order_total_html' );
function mp_wc_cart_totals_order_total_html( $value ) {
	$value = mp_html_class_by_class( $value, 'woocommerce-Price-amount', 'uk-text-large' );
	$value = mp_html_attrs_by_class(
		$value,
		'woocommerce-Price-currencyLabel',
		array(
			'class' => 'uk-text-muted uk-text-lighter uk-margin-small-right',
			'style' => 'font-size: 1rem',
		),
		array( 'hidden' => false )
	);
	return $value;
}

// Style the applied coupon label in the cart order table, including 'Remove' link.
add_filter( 'woocommerce_cart_totals_coupon_label', 'mp_wc_cart_totals_coupon_label', 10, 2 );
function mp_wc_cart_totals_coupon_label( $coupon_label, $coupon ) {
	$coupon_code         = buildAttributes(
		array(
			'class' => 'has-icon uk-text-uppercase uk-text-muted uk-margin-small-left',
		),
		'span',
		get_icon(
			'pricetag',
			array(
				'class' => 'uk-inline',
				'style' => 'margin-right: .2em',
			)
		) . $coupon->get_code()
	);
	$remove_coupon_url   = add_query_arg( 'remove_coupon', rawurlencode( $coupon->get_code() ), Automattic\Jetpack\Constants::is_defined( 'WOOCOMMERCE_CHECKOUT' ) ? wc_get_checkout_url() : wc_get_cart_url() );
	$remove_coupon_attrs = array(
		'class'       => 'woocommerce-remove-coupon uk-text-muted uk-link-text',
		'data-coupon' => $coupon->get_code(),
	);
	$remove_coupon       = icon_link( 'close-circle', 'Remove', $remove_coupon_url, $remove_coupon_attrs, true );

	$coupon_code = buildAttributes( 'uk-text-nowrap uk-flex-inline', 'span', $coupon_code . $remove_coupon );

	$coupon_label = str_replace( ': ' . $coupon->get_code(), $coupon_code, $coupon_label );

	return $coupon_label;
}

// Remove the original 'Remove coupon' link in the cart orders table.
add_filter( 'woocommerce_cart_totals_coupon_html', 'mp_wc_cart_totals_coupon_html', 10, 3 );
function mp_wc_cart_totals_coupon_html( $coupon_html, $coupon, $discount_amount_html ) {
	$coupon_html = mp_html_remove_by_class( $discount_amount_html, 'woocommerce-remove-coupon' );
	return $coupon_html;
}


// Change 'Cart is Empty' message.
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
add_action(
	'woocommerce_cart_is_empty',
	function () {
		echo wp_kses_post( apply_filters( 'wc_empty_cart_message', __( 'Your cart is currently empty.', 'woocommerce' ) ) );
	},
	10
);
