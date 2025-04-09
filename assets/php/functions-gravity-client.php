<?php
/*
  Section: Gravity Forms customizations (client-specific)
  Purpose: Scripts, filters, actions to change how Gravity Forms looks and
		   functions, including the WooCommerce Gravity Forms extension.

  Author: Media Proper
  Last updated: 6 May 2022

*/

// Fieldsets and field containers (.gfield) aren't available in the field_content hook, so we need to do them here.
add_filter(
	'gform_get_form_filter_1',
	function( $form_string, $form ) {

		if ( is_gf_forms_editor() ) {
			return $form_string;
		}

		$form_string = mp_html_class_by_class( $form_string, 'grecaptcha-branding', 'uk-text-center', true );

		return $form_string;

	},
	10,
	2
);
