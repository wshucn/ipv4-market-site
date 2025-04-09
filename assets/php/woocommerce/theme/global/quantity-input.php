<?php
/**
 * WooCommerce Quantity Inputs
 *
 * @package woocommerce
 */

// Effectively hides the quantity field, without forcing product to be sold individually, by setting min and max to 1.
add_filter( 'woocommerce_quantity_input_min', 'mp_wc_quantity_input_hidden', 10, 2 );
add_filter( 'woocommerce_quantity_input_max', 'mp_wc_quantity_input_hidden', 10, 2 );
function mp_wc_quantity_input_hidden( $quantity, $product ) {
	// Only do this on the single product page.
	if ( ! is_product() ) {
		return $quantity;
	}
	return 1;
}


// Quantity input UIkit classes.
add_filter(
	'woocommerce_quantity_input_classes',
	function ( $array, $product ) {
		return to_array(
			buildClass( $array, 'uk-input', 'uk-form-width-xsmall', 'uk-text-center' )
		);
	},
	10,
	2
);

// Quantity inputs: plus and minus buttons.
add_action( 'woocommerce_before_quantity_input_field', 'mp_wc_quantity_minus_sign' );
function mp_wc_quantity_minus_sign() {
	echo wp_kses_post(
		buildAttributes(
			array(
				'type'    => 'button',
				'class'   => 'quantity-minus uk-button uk-button-link uk-margin-small-right',
				'uk-icon' => 'minus-circle',
			),
			'button',
			true
		)
	);
}
add_action( 'woocommerce_after_quantity_input_field', 'mp_wc_quantity_plus_sign' );
function mp_wc_quantity_plus_sign() {
	echo wp_kses_post(
		buildAttributes(
			array(
				'type'    => 'button',
				'class'   => 'quantity-plus uk-button uk-button-link uk-margin-small-left',
				'uk-icon' => 'plus-circle',
			),
			'button',
			true
		)
	);
}


// JavaScript needed to update field when plus/minus buttons are clicked.
add_action( 'woocommerce_after_quantity_input_field', 'mp_wc_quantity_update_script_enqueue' );
function mp_wc_quantity_update_script_enqueue() {
	add_action( 'wp_footer', 'mp_wc_quantity_step_script' );
}

function mp_wc_quantity_step_script() {
	?>
<script type='text/javascript'>
	function maybeDisableButtons( el ) {
		let $qty = jQuery( el ),
			$wrapper = $qty.closest( '.product-quantity' ),
			val = parseFloat($qty.val()),
			max = parseFloat($qty.attr('max')),
			min = parseFloat($qty.attr('min')),
			step = parseFloat($qty.attr('step')) || 1,
			$plus = $wrapper.find( '.quantity-plus' ),
			$minus = $wrapper.find( '.quantity-minus' ),
			nextMinus = $qty.val() - step;

		$plus.prop('disabled', Boolean( max && ( max === $qty.val() ) ));
		$minus.prop('disabled', Boolean( 0 === nextMinus ) || (min && ( min > nextMinus )));
	}

	jQuery( function( $ ) {
		$( 'input.qty' ).each( function(){
			maybeDisableButtons(this);
		});

		$(document.body).on('change', 'input.qty', function(e){
			maybeDisableButtons( e.target );
		});

		$(document.body).on('click', 'button.quantity-plus, button.quantity-minus', function(e) {
			let $target = $( e.target ).closest( 'button, [type="button"]' ),
				$wrapper = $target.closest( '.product-quantity' ),
				$qty = $wrapper.find( 'input.qty' ),
				val = parseFloat($qty.val()),
				max = parseFloat($qty.attr('max')),
				min = parseFloat($qty.attr('min')),
				step = parseFloat($qty.attr('step')) || 1,
				$plus = $wrapper.find( '.quantity-plus' ),
				$minus = $wrapper.find( '.quantity-minus' );

			// Change the value if plus or minus
			if ( $target.is('.quantity-plus') ) {
				if ( max && ( max <= val ) ) {
					$qty.val( max );
				} else {
					$qty.val( val + step ).change();
				}
			} else {
				if ( min && ( min >= val ) ) {
					$qty.val( min );
				} else if ( val > 1 ) {
					$qty.val( val - step ).change();
				}
			}
		});
	});
</script>
	<?php
}


// Auto refresh Buy Now button when quantity changes, so it adds the right number of items to the cart.
function mp_wc_quantity_update_buynow_script() {
	?>
<script type='text/javascript'>
	var timeout;
	jQuery( function( $ ) {
		$(document.body).on('change', 'input.qty', function(e) {

			if ( timeout !== undefined ) {
				clearTimeout( timeout );
			}

			timeout = setTimeout(function() {
				let target = $(e.target);
				let qty = $(target).val();
				let $buy_now = $(target).closest('form.cart').find('a[name="buy-now"]');
				$buy_now.each(function() {
					let href = new URL(this.href);
					let params = new URLSearchParams(href.search.slice(1));
					params.set('quantity', qty);
					this.href = `${href.pathname}?${params}`;
				});
			}, 1000 );

		});
	} );

</script>
	<?php
}

// Auto refresh the cart on quantity changes.
add_action( 'woocommerce_cart_actions', 'mp_wc_quantity_update_cart_script_enqueue' );
function mp_wc_quantity_update_cart_script_enqueue() {
	add_action( 'wp_footer', 'mp_wc_quantity_update_cart_script' );
}
function mp_wc_quantity_update_cart_script() {
	?>
<script type='text/javascript'>
	var timeout;
	jQuery( function( $ ) {
		$('[name="update_cart"]').removeAttr('disabled');

		$(document.body).on('updated_cart_totals', function() {
			$( "[name='update_cart']" ).removeAttr('disabled');
			$( 'input.qty' ).each( function(){
				maybeDisableButtons(this);
			});
		});

		$(document.body).on('change', 'input.qty', function(e){

			if ( timeout !== undefined ) {
				clearTimeout( timeout );
			}

			timeout = setTimeout(function() {
				$('[name="update_cart"]').trigger('click');
			}, 1000 );

		});
	} );
</script>
	<?php
}
