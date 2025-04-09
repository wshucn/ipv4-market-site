<?php
/**
 * WooCommerce Forms & Fields
 *
 * @package woocommerce
 */



// add_filter( 'comment_form_fields', 'mp_comment_form_fields', 9, 1 );
// ...


// add_filter('comment_form_field_cookies', 'mp_comment_form_field_cookies', 10, 1);
// ...


/*
$defaults = array(
	'type'              => 'text',
	'label'             => '',
	'description'       => '',
	'placeholder'       => '',
	'maxlength'         => false,
	'required'          => false,
	'id'                => $key,
	'class'             => array(),		The field's html element wrapper.
	'label_class'       => array(),		The field label.
	'input_class'       => array(),		The field itself.
	'return'            => false,
	'options'           => array(),		For selects.
	'custom_attributes' => array(),		Applied to the field.
	'validate'          => array(),
	'default'           => '',
);
*/

// Add UIkit classes to WC form fields.
add_filter( 'woocommerce_form_field_args', 'mp_wc_form_field_args_uikit_classes', 10, 3 );
function mp_wc_form_field_args_uikit_classes( $args, $key, $value = null ) {

	// Global form label class.
	$args['label_class'][] = 'uk-form-label uk-text-nowrap';

	// Disable LastPass icon because it clashes with our required marker.
	$args['custom_attributes']['data-lpignore'] = true;

	switch ( $args['type'] ) {
		case 'select':      // Targets all select input type elements, except the country and state select input types.
		case 'country':
			// $args['class'][]       = '';
			$args['input_class'] = array( 'uk-select' );
			break;

		case 'state':       // Can also be used for 'County' for UK and other addresses, in which case it is a text input.
			if ( ! empty( $args['country'] ) && class_exists( 'WC_Countries' ) ) {
				$countries = new WC_Countries();
				$states    = $countries->get_states( $args['country'] );
			}
			$args['input_class'] = empty( $states ) ? array( 'uk-input' ) : array( 'uk-select' );
			break;

		case 'password':
		case 'text':
		case 'email':
		case 'tel':
		case 'number':
			$args['input_class'] = array( 'uk-input' );
			break;

		case 'textarea':
			$args['input_class'] = array( 'uk-textarea' );
			break;

		case 'checkbox':
			$args['input_class'] = array( 'uk-checkbox' );
			break;

		case 'radio':
			$args['input_class'] = array( 'uk-radio' );
			break;

		default:
			$args['input_class'] = array( 'uk-input' );
			break;
	}

	return $args;
}


// woocommerce_default_address_fields handles both billing and shipping.
// Since there's no reason for them not to match, handle them here.
add_filter( 'woocommerce_default_address_fields', 'mp_wc_default_address_fields_width', 10, 1 );
function mp_wc_default_address_fields_width( $fields ) {
	$field_widths = apply_filters( 'woocommerce_form_field_width', array() );

	array_walk(
		$fields,
		function( &$v, $k ) use ( $field_widths ) {
			if ( ! empty( $field_widths[ $k ] ) ) {
				$v['class'][] = $field_widths[ $k ];
			}
		}
	);
	return $fields;
}

// Handle any remaining non-address fields here.
add_filter( 'woocommerce_form_field_args', 'mp_wc_form_field_args_width', 10, 3 );
function mp_wc_form_field_args_width( $args, $key, $value = null ) {
	$field_widths = apply_filters( 'woocommerce_form_field_width', array() );

	if ( array_key_exists( $key, $field_widths ) ) {
		$args['class'][] = $field_widths[ $key ];
	} else {
		$translation = array(
			'form-row-wide'  => 'uk-width-1-1',
			'form-row-first' => 'uk-width-1-2@s',
			'form-row-last'  => 'uk-width-1-2@s',
		);
		foreach ( $translation as $from => $to ) {
			if ( hasClass( $from, $args['class'] ) ) {
				$args['class'][] = $to;
			}
		}
	}
	return $args;
}

// Widths for WooCommerce form fields. These are overrides for the default Woo -> UIkit translations.
// So, fields that are normally 50% in WooCommerce will already be 50% and don't need to be so set here.
add_filter(
	'woocommerce_form_field_width',
	function( $field_widths ) {
		return array(
			'account_username' => 'uk-width-1-2@s',
			'account_password' => 'uk-width-1-2@s',
			'billing_phone'    => 'uk-width-1-2@s',
			'billing_email'    => 'uk-width-1-2@s',
			'shipping_phone'   => 'uk-width-1-2@s',
			'shipping_email'   => 'uk-width-1-2@s',
			// Billing and Shipping fields combined.
			// 'country'          => 'uk-width-1-1',
			// 'first_name'       => 'uk-width-1-2@s',
			// 'last_name'        => 'uk-width-1-2@s',
			// 'company'          => 'uk-width-expand@s',
			// 'address_1'        => 'uk-width-1-1',
			// 'address_2'        => 'uk-width-1-1',
			// City, State/County, Postcode set to expand so they always fill space,
			// because some countries re-order them in the form grid!
			'city'             => 'uk-width-expand@s',
			'state'            => 'uk-width-expand@s',
			'postcode'         => 'uk-width-expand@s',
			// 'order_comments'   => 'uk-width-1-1',
		);
	}
);

// Billing and Shipping fields have a unique hook, so that they work on the My Account Edit Address pages.
add_filter( 'woocommerce_billing_fields', 'mp_billing_fields_placeholders' );
function mp_billing_fields_placeholders( $fields ) {
	$fields['billing_email']['placeholder'] = _x( 'Email address', 'placeholder', 'woocommerce' );
	$fields['billing_phone']['placeholder'] = _x( 'Phone', 'placeholder', 'woocommerce' );
	$fields['billing_phone']['clear']       = true;

	return $fields;
}
// add_filter( 'woocommerce_shipping_fields', 'mp_shipping_fields_placeholders' );
// function mp_shipping_fields_placeholders( $fields ) {
// $fields['shipping_phone']['clear']       = true;

// return $fields;
// }


// Add placeholders to checkout fields.
add_filter( 'woocommerce_checkout_fields', 'mp_checkout_fields_placeholders' );
function mp_checkout_fields_placeholders( $fields ) {

	$fields['order']['order_comments']['class'][]     = 'form-row-wide uk-margin-top';
	$fields['order']['order_comments']['placeholder'] = _x( 'Notes regarding your order, such as special delivery instructions.', 'placeholder', 'woocommerce' );
	$fields['order']['order_comments']['priority']    = 150;

	return $fields;
}


// $args['clear'] = true normally clears floats. Since we aren't using floats, it instead
// inserts a 100% width empty div in order to prevent .uk-width-expand from mucking things up.
add_filter( 'woocommerce_checkout_fields', 'mp_checkout_fields_clear' );
function mp_checkout_fields_clear( $fields ) {

	// A clearing div will be placed before the fields specified below.
	// Note that the $fields array is multi-level: $fields['account']['account_username']['clear']!
	// Also, Billing and Shipping fields have their own hooks (woocommerce_billing_fields, etc.), since
	// they also show up in the My Account pages.
	$clear_fields = array(
		'account' => array(
			'account_username',
		),
	);

	foreach ( $clear_fields as $fieldset => $clear_fields ) {
		foreach ( $clear_fields as $clear_field ) {
			if ( array_key_exists( $clear_field, $fields[ $fieldset ] ) ) {
				$fields[ $fieldset ][ $clear_field ]['clear'] = true;
			}
		}
	}

	return $fields;
}


// Add placeholders to address fields
// Our hooked in function - $address_fields is passed via the filter!
add_filter( 'woocommerce_default_address_fields', 'mp_override_default_address_fields', 10, 1 );
function mp_override_default_address_fields( $address_fields ) {
	$address_fields['first_name']['required'] = false;

	$address_fields['first_name']['placeholder'] = _x( 'First name', 'placeholder', 'woocommerce' );
	$address_fields['last_name']['placeholder']  = _x( 'Last name', 'placeholder', 'woocommerce' );
	$address_fields['company']['label']          = __( 'Business or organization', 'woocommerce' );
	$address_fields['company']['placeholder']    = _x( 'Business or organization', 'placeholder', 'woocommerce' );
	$address_fields['address_1']['label']        = __( 'Address', 'woocommerce' );
	$address_fields['address_1']['placeholder']  = _x( 'Address', 'placeholder', 'woocommerce' );
	$address_fields['address_2']['label']        = __( 'Apartment, suite, etc.', 'woocommerce' );
	$address_fields['address_2']['placeholder']  = _x( 'Apartment, suite, etc.', 'placeholder', 'woocommerce' );
	$address_fields['address_2']['label_class']  = array(); // Remove default screen-reader-text.
	$address_fields['city']['placeholder']       = _x( 'Town / City', 'placeholder', 'woocommerce' );
	$address_fields['state']['placeholder']      = _x( 'State', 'placeholder', 'woocommerce' );
	$address_fields['postcode']['label']         = __( 'ZIP', 'woocommerce' );
	$address_fields['postcode']['placeholder']   = _x( 'ZIP', 'placeholder', 'woocommerce' );

	return $address_fields;
}


// Label formatting.
// add_filter( 'woocommerce_default_address_fields', 'mp_wc_default_address_fields_label', 10, 1 );
function mp_wc_default_address_fields_label( $fields ) {
	// $fields['address_2']['label_class'][] = 'screen-reader-text';
	return $fields;
}
add_filter( 'woocommerce_form_field_args', 'mp_wc_form_field_args_label', 10, 3 );
function mp_wc_form_field_args_label( $args, $key, $value = null ) {

	// For absolute positioned labels.
	$args['class'][] = 'uk-position-relative';

	return $args;
}

add_filter( 'woocommerce_form_field', 'mp_wc_form_field', 10, 4 );
function mp_wc_form_field( $field, $key, $args, $value ) {

	// Make all form rows into <div>s.
	$field = mp_html_attrs( $field, '.form-row', array(), true, 'div' );

	// Display block on input wrappers.
	$field = mp_html_class_by_class( $field, 'woocommerce-input-wrapper', 'uk-display-block uk-form-controls', true );

	// Rather than clearfix, insert an empty 100% width div to clear flex row.
	if ( ! empty( $args['clear'] ) && true === $args['clear'] ) {
		$priority  = $args['priority'];
		$clear_div = buildAttributes(
			array(
				'data-priority' => $priority,
				'class'         => 'form-row form-row-wide uk-divider uk-margin-remove-top uk-width-1-1',
			),
			'div'
		);
		$field     = $clear_div . $field;
	}

	return $field;
}

// Enable autogrowing textareas.
add_filter( 'woocommerce_form_field', 'mp_wc_form_field_textarea_autogrow', 10, 4 );
function mp_wc_form_field_textarea_autogrow( $field, $key, $args, $value ) {

	if ( 'textarea' === $args['type'] ) {
		$field = mp_wrap_element( $field, '.uk-textarea', 'div', 'textarea-autogrow' );
	}
	return $field;
}

add_filter( 'woocommerce_form_field_args', 'mp_wc_form_field_args_has_input', 10, 3 );
function mp_wc_form_field_args_has_input( $args, $key, $value = null ) {
	if ( ! empty( $value ) ) {
		$args['class'][] = 'has_input';
	}
	return $args;
}

// ARIA accessibility for form fields.
add_filter( 'woocommerce_form_field_args', 'mp_wc_form_field_args_aria', 10, 3 );
function mp_wc_form_field_args_aria( $args, $key, $value = null ) {

	// aria-required.
	if ( ! empty( $args['required'] ) && true === $args['required'] ) {
		$args['custom_attributes']['aria-required'] = true;
	}

	return $args;
}

// ARIA accessiblity for form fields.
add_filter( 'woocommerce_form_field', 'mp_wc_form_field_aria', 10, 4 );
function mp_wc_form_field_aria( $field, $key, $args, $value ) {

	// aria-labelledby.
	$field = mp_html_attrs( $field, '//label[contains(@class, "uk-form-label")]', array( 'id' => "{$key}-label" ), true );
	$field = mp_html_attrs( $field, "//*[@id='{$key}']", array( 'aria-labelledby' => "{$key}-label" ), true );

	// Add an icon to the field description.
	$icon  = get_icon( 'help-circle-outline', '', 'small' );
	$field = mp_move_element( $field, $icon, 'span[contains(@class, "description")]', 'firstChild' );

	// Show aria-descriptions, which are usually hidden until the field is focused.
	$field = mp_html_attrs(
		$field,
		'span[contains(@class, "description")]',
		array(
			'class'       => '!description uk-text-meta has-icon',
			'aria-hidden' => 'false',
			'style'       => 'padding-left: 1.5em; margin-top: 4px;',
		),
		true,
		'div'
	);

	return $field;

}

// Add UIkit class to product attribute selects, which aren't covered by the woocommerce_form_field_args filter.
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'mp_wc_dropdown_variation_attribute_options_args' );
function mp_wc_dropdown_variation_attribute_options_args( $args ) {
	$args['class'] = empty( $args['class'] ) ? 'uk-select' : $args['class'] . ' uk-select';
	return $args;
}


// Add has_input class to shipping calculator fields that have input.
add_action( 'woocommerce_after_shipping_calculator', 'mp_shipping_calculator_init_script_enqueue', 20 );
function mp_shipping_calculator_init_script_enqueue() {
	add_action( 'wp_footer', 'mp_shipping_calculator_init_script' );
}
function mp_shipping_calculator_init_script() {
	?>
	<script type='text/javascript'>
	jQuery( document ).on(
		'click',
		'.shipping-calculator-button',
		function() {
			jQuery( '.shipping-calculator-form' ).find( '.uk-input, .uk-select' ).trigger( 'change' );
		}
	);
	jQuery( function( $ ) {
		$( '._shipping-calculator-form' ).find( '.uk-input, .uk-select' ).trigger( 'change' );
	});
	</script>
	<?php
}

// Add UIkit form element classes to WooCommerce AJAX-created elements.
add_action( 'woocommerce_after_checkout_billing_form', 'mp_billing_country_state_field_class_script_enqueue', 20 );
add_action( 'woocommerce_after_checkout_shipping_form', 'mp_billing_country_state_field_class_script_enqueue', 20 );
add_action( 'woocommerce_after_edit_account_address_form', 'mp_billing_country_state_field_class_script_enqueue', 20 );
add_action( 'woocommerce_after_shipping_calculator', 'mp_billing_country_state_field_class_script_enqueue', 20 );
function mp_billing_country_state_field_class_script_enqueue() {
	add_action( 'wp_footer', 'mp_billing_country_state_field_class_script' );
}
function mp_billing_country_state_field_class_script() {
	?>
	<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$( document.body ).on( 'country_to_state_changing', function( e, country, $wrapper ) {

			// Target Billing and Shipping fields, and Shipping Calculator
			var $stateField = $wrapper.find('#billing_state, #shipping_state, #calc_shipping_state').not('[hidden]');

			var UIkit_class = ( $stateField.is( 'select' ) ) ? 'uk-select' : 'uk-input';
			var fieldTimeout = setTimeout(function() {
				// Ensure the select placeholder is correct, even though we use gettext hook to brute force it.
				var placeholder = $stateField.attr('placeholder');
				$stateField.find('option:first').text(placeholder);
				// Add style
				$stateField
					.attr( 'class', UIkit_class )
					.attr('data-lpignore', 'true')
					.trigger('blur');
				$stateField.closest("[id$='_field'],[id^='field_']")
					// .removeClass('has_input') // there may be an initial value, so we can't do this.
					.removeClass('woocommerce-invalid')
					.removeClass('woocommerce-invalid-required-field');
			}, 10);
		});
		// Hide disabled State/Country selects.
		$('input[type="hidden"]').closest('.address-field').hide();
	});
	</script>
	<?php
}

// Add placeholder values for State, City, and Postcode.
add_filter( 'woocommerce_get_country_locale', 'mp_wc_woocommerce_get_country_locale', 10, 1 );
function mp_wc_woocommerce_get_country_locale( $locales ) {

	foreach ( $locales as $key => $value ) {
		if ( 'GB' === $key ) {
			$locales[ $key ]['state']['required'] = true;
			$locales[ $key ]['state']['hidden']   = false;
		}
		foreach ( array( 'state', 'city', 'postcode' ) as $field ) {
			if ( array_key_exists( $field, $locales[ $key ] ) && empty( $locales[ $key ][ $field ]['placeholder'] ) ) {
				if ( ! empty( $locales[ $key ][ $field ]['label'] ) ) {
					$locales[ $key ][ $field ]['placeholder'] = $locales[ $key ][ $field ]['label'];
				}
			}
		}
	}

	return $locales;
}

// Change the State dropdown placeholder text.
add_filter( 'gettext', 'mp_wc_select_placeholder', 100, 3 );
function mp_wc_select_placeholder( $translated_text, $text, $domain ) {
	// Maybe alter this logic if this is catching unintended strings.
	if ( is_checkout() || is_account_page() || is_cart() ) {
		if ( 'Select an option&hellip;' === $text && 'woocommerce' === $domain ) {
			$translated_text = 'State';
		}
	}
	return $translated_text;
}
