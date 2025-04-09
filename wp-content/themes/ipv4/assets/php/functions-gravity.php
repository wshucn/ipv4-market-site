<?php
/*
  Section: Gravity Forms customizations
  Purpose: Scripts, filters, actions to change how Gravity Forms looks and
		   functions, including the WooCommerce Gravity Forms extension.

  Author: Media Proper
  Last updated: 9 December 2020

*/

add_filter( 'gform_disable_form_theme_css', '__return_true' );

// This is always a blank string. There are no hooks on the field-specific $input.
add_filter( 'gform_field_input', 'mp_gf_field_input', 10, 5 );
function mp_gf_field_input( $input, $field, $value, $lead_id, $form_id ) {
	return $input;
}

add_filter( 'gform_field_css_class', 'mp_gf_field_css_class', 10, 3 );
function mp_gf_field_css_class( $class, $field, $form ) {
	// gfield gfield--width-full gfield_contains_required field_sublabel_below field_description_below gfield_visibility_visible
	// str_replace with an array of translations.
	// classes for the field container, so mainly just grid classes
	// gfield--width-full
	// gfield--width-eleven-twelfths
	// gfield--width-five-sixths
	// gfield--width-three-quarter
	// gfield--width-two-thirds
	// gfield--width-seven-twelfths
	// gfield--width-half
	// gfield--width-five-twelfths
	// gfield--width-third
	// gfield--width-quarter
	return $class;
}

// This is the only way to hook into field content.
// add_filter( 'gform_field_content', 'mp_gf_field_content', 10, 5 );
// function mp_gf_field_input( $field_content, $field, $value, $entry_id, $form_id ) {
// return $field_content;
// }

// add_filter( 'ninja_forms_display_form_settings', 'mp_nf_display_form_settings', 10, 2 );
function mp_nf_display_form_settings( $settings, $form_id ) {
	pp( $settings );
	return $settings;
}

add_filter( 'ninja_forms_localize_fields_preview', 'mp_nf_localize_fields', 10, 1 );
function mp_nf_localize_fields( $field ) {
	$class = $field['settings']['element_class'];
	switch ( $field['settings']['type'] ) {
		case 'textbox':
		case 'email':
			$class .= ' uk-input';
			break;
		case 'textarea':
			$class .= ' uk-textarea';
			break;
		case 'submit':
			$class .= ' uk-button';
			break;
	}
	$field['settings']['element_class'] = $class;
	pp( $field );
	return $field;
}

// add_filter( 'ninja_forms_preview_display_field', 'mp_nf_preview_display_field', 10, 1 );
function mp_nf_preview_display_field( $field ) {
	$field['settings']['element_class'] .= ' uk-input';
	pp( $field );
	return $field;
}

// add_filter( 'ninja_forms_display_fields', 'mp_nf_display_fields', 10, 2 );
function mp_nf_display_fields( $fields, $form_id ) {
	pp( $fields );
	return $fields;
}

// add_action(
// 'nf_display_enqueue_scripts',
// function () {
// wp_dequeue_style( 'nf-font-awesome' );
// wp_dequeue_style( 'nf-display' );
// }
// );


// Helper function to determine if we're in the form editor.
function is_gf_forms_editor() {
	if ( ! class_exists( 'GFCommon' ) ) {
		return false; }
	$is_form_editor  = GFCommon::is_form_editor();
	$is_entry_detail = GFCommon::is_entry_detail();
	return $is_form_editor || $is_entry_detail;
}

/**
 * Change the form validation error message.
 */
add_filter(
	'gform_validation_message',
	function ( $message, $form ) {
		return "<div class='validation_error'>" . __( 'Oh no! Some fields appear wrong. Please check and send again.', 'text_domain' ) . '</div>';
	},
	10,
	2
);


// Change the spinner element
add_filter(
	'gform_ajax_spinner_url',
	function ( $image_src, $form ) {
		return get_asset_url( 'images/spinner-light.svg' );
	},
	10,
	2
);


// Add a rows setting to textarea fields
add_action( 'gform_field_appearance_settings', 'mp_gform_textarea_appearance_settings_rows', 10, 2 );
function mp_gform_textarea_appearance_settings_rows( $position, $form_id ) {
	if ( $position == 500 ) {
		?>
<li class="textarea_rows_setting field_setting">
	<label for="textarea_rows" class='section_label'>
		<?php _e( 'Number of Rows', 'text_domain' ); ?>
	</label>
	<input type="number" min="1" max="10" id="textarea_rows" onchange="SetFieldProperty('textareaRows', this.value);"
		class="fieldwidth-3" />
</li>
		<?php
	}
}

add_action( 'gform_editor_js', 'mp_gform_textarea_appearance_settings_rows_script' );
function mp_gform_textarea_appearance_settings_rows_script() {
	?>
<script type='text/javascript'>
	fieldSettings.textarea += ", .textarea_rows_setting";
	jQuery(document).on("gform_load_field_settings", function(event, field, form) {
		jQuery('#textarea_rows').val(field["textareaRows"]);
	});
</script>
	<?php
}


// Change textarea rows to a number specified by user
add_filter(
	'gform_field_content',
	function ( $field_content, $field ) {
		if ( $field->type == 'textarea' ) {
			$rows          = ! empty( $field->textareaRows ) ? (int) $field->textareaRows : '1';
			$field_content = mp_html_attrs( $field_content, 'textarea', array( 'rows' => $rows ) );
		}
		return $field_content;
	},
	10,
	2
);



// Add a Largeness field setting to the Field Settings panel
add_action( 'gform_field_appearance_settings', 'mp_gform_field_appearance_settings_largeness', 10, 2 );
function mp_gform_field_appearance_settings_largeness( $position, $form_id ) {
	$choices = array(
		array(
			'value' => '',
			'text'  => esc_html__( 'Use form setting', 'text_domain' ),
		),
		array(
			'value' => 'default',
			'text'  => esc_html__( 'Default', 'text_domain' ),
		),
		array(
			'value' => 'uk-form-small',
			'text'  => esc_html__( 'Small', 'text_domain' ),
		),
		array(
			'value' => 'uk-form-large',
			'text'  => esc_html__( 'Large', 'text_domain' ),
		),
	);

	if ( $position == 500 ) {
		?>
<li class="field_largeness_setting field_setting">
	<label for="field_largeness" class='section_label'>
		<?php _e( 'Field Largeness', 'text_domain' ); ?>
		<?php gform_tooltip( 'form_field_largeness_value' ); ?>
	</label>
	<select id="field_largeness" onchange="SetFieldProperty('fieldLargeness', jQuery(this).val());">
		<?php foreach ( $choices as $choice ) : ?>
		<option value="<?php echo $choice['value']; ?>">
			<?php echo $choice['text']; ?>
		</option>
		<?php endforeach; ?>
	</select>
</li>
		<?php
	}
}

// JavaScript is needed to set the field type(s) and default value for the setting, and to populate the value
add_action( 'gform_editor_js', 'mp_gform_field_appearance_settings_largeness_script' );
function mp_gform_field_appearance_settings_largeness_script() {
	?>
<script type='text/javascript'>
	// Add the Largeness setting to the following fields.
	fieldSettings.color += ", .field_largeness_setting";
	fieldSettings.name += ", .field_largeness_setting";
	fieldSettings.email += ", .field_largeness_setting";
	fieldSettings.month += ", .field_largeness_setting";
	fieldSettings.number += ", .field_largeness_setting";
	fieldSettings.password += ", .field_largeness_setting";
	fieldSettings.phone += ", .field_largeness_setting";
	fieldSettings.search += ", .field_largeness_setting";
	fieldSettings.tel += ", .field_largeness_setting";
	fieldSettings.text += ", .field_largeness_setting";
	fieldSettings.textarea += ", .field_largeness_setting";
	fieldSettings.time += ", .field_largeness_setting";
	fieldSettings.url += ", .field_largeness_setting";
	fieldSettings.week += ", .field_largeness_setting";
	fieldSettings.radio += ", .field_largeness_setting";
	fieldSettings.checkbox += ", .field_largeness_setting";

	// Set the default value.
	SetDefaultValues_color = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_name = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_email = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_month = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_number = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_password = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_phone = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_search = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_tel = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_text = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_textarea = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_time = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_url = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_week = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_radio = function(field) {
		field.fieldLargeness = '';
	}
	SetDefaultValues_checkbox = function(field) {
		field.fieldLargeness = '';
	}

	// Populate the field value when the fields are loaded/displayed.
	jQuery(document).on("gform_load_field_settings", function(event, field, form) {
		jQuery('#field_largeness').val(field["fieldLargeness"]);
	});
</script>
	<?php
}

// Set the tooltip text for the Largeness field setting
add_filter( 'gform_tooltips', 'mp_gform_field_appearance_settings_largeness_tooltips', 10, 1 );
function mp_gform_field_appearance_settings_largeness_tooltips( $tooltips ) {
	$tooltip_label               = 'Field Largeness';
	$tooltip_text                = 'This affects the height and font size of the field. Choose from default, small, or large.';
	$tooltips['form_field_size'] = sprintf( '<strong>%s</strong>%s', __( $tooltip_label, 'text_domain' ), __( $tooltip_text, 'text_domain' ) );
	return $tooltips;
}

// Enable widths such as half, third, quarter for Gravity fields.
// Note: these don't seem to be available for File Upload fields. But you can add the CSS classes manually.
add_filter( 'gform_field_container', 'mp_gform_field_appearance_settings_width', 10, 6 );
function mp_gform_field_appearance_settings_width( $field_container, $field, $form, $css_class, $style, $field_content ) {
	if ( is_gf_forms_editor() ) {
		return $field_container;
	}

	// Don't add any width classes to hidden fields.
	if ( hasClass( 'hidden', $css_class ) ) {
		return $field_container;
	}

	// Allow for positioning within form editor. Dirty: we just override the field width when a CSS class is present.
	if ( hasClass( 'gfield--width-half', $css_class ) ) {
		$field->size = 'half';
	} elseif ( hasClass( 'gfield--width-third', $css_class ) ) {
		$field->size = 'third';
	} elseif ( hasClass( 'gfield--width-two-thirds', $css_class ) ) {
		$field->size = 'two-thirds';
	} elseif ( hasClass( 'gfield--width-quarter', $css_class ) ) {
		$field->size = 'quarter';
	} elseif ( hasClass( 'gfield--width-three-quarter', $css_class ) ) {
		$field->size = 'three-quarter';
	}

	switch ( $field->size ) {
		case 'xsmall':
			$li_class = array( 'uk-form-width-xsmall' );
			break;
		case 'small':
			$li_class = array( 'uk-form-width-small' );
			break;
		case 'medium':
			$li_class = array( 'uk-form-width-medium' );
			break;
		case 'large':
			$li_class = array( 'uk-form-width-large' );
			break;
		case 'expand':
			$li_class = array( 'uk-width-expand' );
			break;
		case 'half':
			$li_class = array( 'uk-width-1-2@m', 'width-1-2' );
			break;
		case 'third':
			$li_class = array( 'uk-width-1-3@m', 'width-1-3' );
			break;
		case 'two-thirds':
			$li_class = array( 'uk-width-2-3@m', 'width-2-3' );
			break;
		case 'quarter':
			$li_class = array( 'uk-width-1-4@m', 'width-1-4' );
			break;
		case 'three-quarter':
			$li_class = array( 'uk-width-3-4@m', 'width-3-4' );
			break;
		default:
			$li_class = esc_html( $field->size );
	}
	return empty( $li_class ) ? $field_container : mp_html_class_by_class( $field_container, 'gfield', buildClass( $li_class ), true );
}

add_filter( 'gform_tooltips', 'mp_gform_field_appearance_settings_width_tooltips', 10, 1 );
function mp_gform_field_appearance_settings_width_tooltips( $tooltips ) {
	$tooltip_label               = 'Field Size';
	$tooltip_text                = 'Select a form field size from the available options. This will set the width of the field.';
	$tooltips['form_field_size'] = sprintf( '<strong>%s</strong>%s', __( $tooltip_label, 'text_domain' ), __( $tooltip_text, 'text_domain' ) );
	return $tooltips;
}




// Add Form Size settings
add_filter(
	'gform_form_settings_fields',
	function ( $fields, $form ) {
		$before = \array_slice( $fields['form_layout']['fields'], 0, 1 );
		// (new fields will go here)
		$after = \array_slice( $fields['form_layout']['fields'], 1 );

		// Form Largeness
		$new_fields[]                    = array(
			'type'          => 'radio',
			'name'          => 'formLargeness',
			'label'         => esc_html__( 'Form Largeness', 'text_domain' ),
			'default_value' => '',
			'horizontal'    => true,
			'tooltip'       => esc_html__( 'Choose the size of form fields. This setting can be overridden for each field.', 'text_domain' ),
			'choices'       => array(
				array(
					'label' => esc_html__( 'Default', 'text_domain' ),
					'name'  => 'default',
					'value' => '',
				),
				array(
					'label' => esc_html__( 'Small', 'text_domain' ),
					'name'  => 'small',
					'value' => 'uk-form-small',
				),
				array(
					'label' => esc_html__( 'Large', 'text_domain' ),
					'name'  => 'large',
					'value' => 'uk-form-large',
				),
			),
		);
		$new_fields                      = array_merge( $new_fields, $after );
		$fields['form_layout']['fields'] = array_merge( $before, $new_fields );

		return $fields;
	},
	10,
	2
);




// Add Form Button settings
add_filter(
	'gform_form_settings_fields',
	function ( $fields, $form ) {
		$before = \array_slice( $fields['form_button']['fields'], 0, -1 );
		// (new fields will go here)
		$after = \array_slice( $fields['form_button']['fields'], -1 );

		// Button Style/Color
		$new_fields[] = array(
			'type'          => 'radio',
			'name'          => 'buttonColor',
			'label'         => esc_html__( 'Button Style', 'text_domain' ),
			'default_value' => 'uk-button-default',
			'horizontal'    => true,
			'choices'       => array(
				array(
					'label' => esc_html__( 'Default', 'text_domain' ),
					'name'  => 'default',
					'value' => 'uk-button-default',
				),
				array(
					'label' => esc_html__( 'Primary', 'text_domain' ),
					'name'  => 'primary',
					'value' => 'uk-button-primary',
				),
				array(
					'label' => esc_html__( 'Secondary', 'text_domain' ),
					'name'  => 'secondary',
					'value' => 'uk-button-secondary',
				),
				array(
					'label' => esc_html__( 'Danger', 'text_domain' ),
					'name'  => 'danger',
					'value' => 'uk-button-danger',
				),
				array(
					'label' => esc_html__( 'Text', 'text_domain' ),
					'name'  => 'text',
					'value' => 'uk-button-text',
				),
				array(
					'label' => esc_html__( 'Link', 'text_domain' ),
					'name'  => 'link',
					'value' => 'uk-button-link',
				),
			),
		);

		// Button size
		$new_fields[] = array(
			'type'          => 'radio',
			'name'          => 'buttonSize',
			'label'         => esc_html__( 'Button Size', 'text_domain' ),
			'default_value' => '',
			'horizontal'    => true,
			'choices'       => array(
				array(
					'label' => esc_html__( 'Default', 'text_domain' ),
					'name'  => 'default',
					'value' => '',
				),
				array(
					'label' => esc_html__( 'Small', 'text_domain' ),
					'name'  => 'small',
					'value' => 'uk-button-small',
				),
				array(
					'label' => esc_html__( 'Large', 'text_domain' ),
					'name'  => 'large',
					'value' => 'uk-button-large',
				),
			),
		);

		// Button icon
		$new_fields[] = array(
			'type'    => 'text',
			'name'    => 'buttonIcon',
			'label'   => esc_html__( 'Button Icon', 'text_domain' ),
			'class'   => 'small',
			'tooltip' => esc_html__( 'Specify a UIkit icon (<code>icon:heart</code>), an Ionicon (<code>heart</code>), or FontAwesome icon (<code>fa-heart</code>).', 'text_domain' ),
		);

		$new_fields[] = array(
			'type'    => 'toggle',
			'name'    => 'buttonIconAfter',
			'label'   => esc_html__( 'Icon After Text', 'text_domain' ),
			'tooltip' => esc_html__( 'Specify whether the icon is displayed after the button text.', 'text_domain' ),
		);

		// Button size
		$new_fields[] = array(
			'type'  => 'text',
			'name'  => 'buttonClass',
			'label' => esc_html__( 'CSS Class Name', 'gravity' ),
			'class' => 'large',
		);

		$new_fields                      = array_merge( $new_fields, $after );
		$fields['form_button']['fields'] = array_merge( $before, $new_fields );

		return $fields;
	},
	10,
	2
);




// Hide hidden fields
add_filter( 'gform_field_container', 'mp_gform_field_container_hidden', 10, 6 );
function mp_gform_field_container_hidden( $field_container, $field, $form, $css_class, $style, $field_content ) {
	if ( is_gf_forms_editor() ) {
		return $field_container;
	}
	if ( hasClass( 'gform_hidden', $css_class ) ) {
		$field_container = mp_html_attrs_by_class( $field_container, 'gfield', 'hidden' );
	}
	return $field_container;
}

// Divide a form by Gravity Forms 'Section' elements.
// This is for facilitating multipage forms when Gravity's own paging is inconvenient.
// add_filter( 'gform_field_container', 'mp_gform_field_container_sections', 10, 6 );
function mp_gform_field_container_sections( $field_container, $field, $form, $css_class, $style, $field_content ) {
	if ( is_gf_forms_editor() ) {
		return $field_container;
	}

	$id       = $field->id;
	$form_id  = (int) rgar( $form, 'id' );
	$field_id = is_admin() || empty( $form ) ? "field_$id" : 'field_' . $form_id . "_$id";
	$tabindex = GFCommon::is_form_editor() ? "tabindex='0'" : '';
	$default  = "<div id='$field_id' {$tabindex} class='{$css_class}' $style>{FIELD_CONTENT}</div>";
	if ( is_gf_forms_editor() ) {
		return $default;
	}

	if ( $field->type == 'section' ) {
		$ul_classes      = array( 'gform_fields' );
		$section_classes = array( 'gform_section' );
		if ( ! empty( $form ) ) {
			// tag the new ul elements identically to the original ul
			array_push(
				$ul_classes,
				$form['labelPlacement'],
				'form_sublabel_' . $form['subLabelPlacement'],
				'description_' . $form['descriptionPlacement']
			);
		}
		return '</div><div class="' . buildClass( $section_classes ) . '">{FIELD_CONTENT}</div><div class="' . buildClass( $ul_classes ) . '">';
	} else {
		return $default;
	}
}



/*
  Section: Add UIkit classes to Gravity Forms fields

*/

// Add UIkit sizes to field size choices
add_filter(
	'gform_field_size_choices',
	function ( $choices ) {
		$choices = array(
			array(
				'value' => 'uk-width-1-1',
				'text'  => 'Full',
			),
			array(
				'value' => 'expand',
				'text'  => 'Expand',
			),
			array(
				'value' => 'xsmall',
				'text'  => 'X-Small',
			),
			array(
				'value' => 'small',
				'text'  => 'Small',
			),
			array(
				'value' => 'medium',
				'text'  => 'Medium',
			),
			array(
				'value' => 'large',
				'text'  => 'Large',
			),
			array(
				'value' => 'half',
				'text'  => 'Half',
			),
			array(
				'value' => 'third',
				'text'  => 'Third',
			),
			array(
				'value' => 'quarter',
				'text'  => 'Quarter',
			),
			array(
				'value' => 'three-quarter',
				'text'  => 'Three-Quarter',
			),
		);
		return $choices;
	}
);

// Replace required label <span> element with <abbr>, which makes more semantic
// sense and is consistent with WooCommerce.
add_filter( 'gform_field_content', 'mp_gform_field_content_required_abbr', 10, 2 );
function mp_gform_field_content_required_abbr( $field_content, $field ) {
	if ( is_gf_forms_editor() ) {
		return $field_content;
	}

	$field_content = mp_html_class_by_class( $field_content, 'span.gfield_required', 'required', true, 'abbr' );
	return $field_content;
}

/**
 * Hide gform_required_legend
*/
add_filter( 'gform_get_form_filter', 'mp_gform_get_form_hide_required', 10, 2 );
function mp_gform_get_form_hide_required( $form_string, $form ) {
	if ( is_gf_forms_editor() ) {
		return $form_string;
	}

	$form_string = mp_html_attrs_by_class( $form_string, 'gform_required_legend', 'hidden' );
	$form_string = mp_html_remove_by_class( $form_string, 'abbr.required' );

	// $form_string = mp_html_attrs_by_class($form_string, "gform_fields", ['uk-margin' => NULL], TRUE);
	// $form_string = str_replace("id='gform_fields", "uk-margin id='gform_fields", $form_string);
	return $form_string;
}


// Add UIkit classes, icons, etc. to different types of inputs
add_filter( 'gform_field_content', 'mp_gform_field_content_uikit', 10, 5 );
function mp_gform_field_content_uikit( $field_content, $field, $value, $entry_id, $form_id ) {
	if ( class_exists( 'GFCommon' ) && GFCommon::is_form_editor() ) {
		return $field_content;
	}

	$input_class = array();
	$input_attrs = array();

	// List of input-type fields.
	$inputs = array(
		'color',
		'email',
		'month',
		'number',
		'password',
		'phone',
		'search',
		'tel',
		'text',
		'time',
		'url',
		'week',
		'name',
		'address',
		'fileupload',
	);

	// Get the Form object, so we can access its settings
	$form = GFAPI::get_form( $form_id );

	$is_left_labels = ( $form['labelPlacement'] === 'left_label' );

	// Add field sizes (fieldLargeness) class to inputs
	// https://getuikit.com/docs/form#size-modifiers
	// Field Largeness can be set globally for the entire form. But each field can override it.
	$field_largeness = ! empty( $form['formLargeness'] ) ? $form['formLargeness'] : 'default';

	if ( ! empty( $field->fieldLargeness ) ) {
		$field_largeness = ( 'default' === $field->fieldLargeness ) ? '' : $field->fieldLargeness;
	}

	// Only apply uk-form-{small, large} to inputs, textareas, and selects.
	if ( in_array( $field->type, array_merge( $inputs, array( 'textarea', 'select' ) ) ) ) {
		$input_class[] = $field_largeness;
	}

	// Add UIkit classes
	// Advanced/complex fields such as Name and Address, which are wrapped in fieldsets,
	// don't for some reason have any Gravity classes on them. So we target those more
	// broadly, near the end of the function.
	$gravity_to_uikit = array(
		'gform_hidden'              => 'uk-hidden',
		'gfield_hidden_product'     => 'uk-hidden',
		'ginput_container_textarea' => 'textarea-autogrow',
		'textarea'                  => 'uk-textarea textarea',
		'ginput_complex'            => 'uk-grid uk-grid-small',
		'gf_name_has_2'             => 'uk-child-width-1-2@m',
		'ginput_full'               => 'uk-width-1-1',
		'ginput_left'               => 'uk-width-1-2@m',
		'ginput_right'              => 'uk-width-1-2@m',
		// 'ginput_container_fileupload'   => 'uk-display-block uk-form-custom',
		'gform_fileupload_rules'    => 'uk-text-meta uk-display-block',
	);
	foreach ( $gravity_to_uikit as $old_class => $new_class ) {
		$new_attrs = array();
		$tag       = null;

		// Add uk-grid attribute where needed.
		if ( hasClass( 'uk-grid', $new_class ) ) {
			$new_attrs[] = 'uk-grid';
		}

		// uk-form-label should always have a label tag
		if ( hasClass( 'uk-form-label', $new_class ) ) {
			$tag = 'label';
		}

		$new_attrs['class'] = $new_class;
		$field_content      = mp_html_attrs_by_class( $field_content, $old_class, $new_attrs, true, $tag );
	}

	// Fields are flex.
	$field_content = mp_html_class_by_class( $field_content, 'gform_fields', 'uk-grid uk-grid-small', true );

	// Add standard 'uk-input' class to inputs
	if ( in_array( $field->type, $inputs ) ) {
		$input_class[] = 'uk-input';
	}
	if ( 'select' === $field->type ) {
		$input_class[] = 'uk-select';
	}

	// Add a placeholder to text inputs and textareas if there isn't one already
	// The required asterisk CSS styles rely on 'placeholder-shown' to determine if an input is empty, so we need this
	// unless we want to use a Javascript solution.
	if ( in_array( $field->type, array_merge( $inputs, array( 'textarea' ) ), true ) && empty( $field->placeholder ) ) {
		// Use an empty placeholder except where the label is not visible
		if ( is_array( $field->inputs ) || ( empty( $field->label ) || 'hidden_label' === $field->labelPlacement ) ) {
			$input_attrs['placeholder'] = ' ';
		} else {
			$input_attrs['placeholder'] = esc_html__( $field->label, 'text_domain' );
		}
	}

	// Make field legends into labels
	// $field_content = mp_html_class_by_class( $field_content, 'legend.gfield_label', 'uk-form-label', true );

	// Wrap input containers in uk-form-controls
	$field_content = mp_wrap_element( $field_content, '.ginput_container', 'div', array( 'class' => 'uk-form-controls' ) );

	// Move label before .uk-form-controls; so that top-aligned descriptions don't interfere with dynamic label positioning.
	$field_content = mp_move_element( $field_content, 'label.gfield_label', 'div.uk-form-controls', 'insertBefore' );

	// Style field description and instruction.
	$field_content = mp_html_class( $field_content, '.gfield_description', 'uk-text-small uk-text-bold uk-text-emphasis', true );
	$field_content = mp_html_class( $field_content, '//div[starts-with(@id, "gfield_instruction")]', 'uk-visible@s uk-text-muted-dark has-icon', true, 'span' );
	$field_content = mp_move_element( $field_content, get_icon( 'information-circle-outline' ), '//span[starts-with(@id, "gfield_instruction")]', 'firstChild' );

	// Add top margin to sections.
	$field_content = mp_html_class( $field_content, '.gsection', 'uk-margin-large-top', true );

	// TODO: Form field icons
	switch ( $field->type ) {

		case 'radio':
		case 'checkbox':
			// wrap checkbox/radio text in .uk-form-controls-text

			$is_radio = ( 'radio' === $field->type );
			$radio_id = $field->formId . '_' . $field->id;

			// move inputs within their labels
			foreach ( $field->choices as $i => $choice ) {
				if ( ! $is_radio ) {
					++$i;
				}
				$choice_id     = sprintf( 'choice_%s_%s', $radio_id, $i );
				$label_id      = sprintf( 'label_%s_%s', $radio_id, $i );
				$field_content = mp_move_element( $field_content, "//input[@id='{$choice_id}']", "//label[@id='{$label_id}']", 'firstChild' );
			}

			// add proper UIkit class
			$field_content = mp_html_class( $field_content, "//input[@type='{$field->type}']", "uk-{$field->type} uk-margin-small-right", true );

			// if label is really long, don't float it.
			if ( strlen( $field->label ) > 24 ) {
				$field_content = mp_html_attrs_by_class(
					$field_content,
					'.uk-form-label',
					array(
						'class' => 'uk-margin-small-bottom',
						'style' => 'float: none; width: initial',
					),
					true
				);
				$field_content = mp_html_class_by_class( $field_content, '.uk-form-controls', '!uk-form-controls', true );
				$field_content = mp_html_class( $field_content, "#input_{$radio_id}", 'uk-grid uk-grid-row-collapse', true );
			}

			// Add 'uk-form-label' to <legend>
			$field_content = mp_html_class_by_class( $field_content, 'legend.gfield_label', 'uk-form-label', true );

			break;

		case 'name':
			// Complex field.
			// legend -> label
			// labels -> hidden (not ideal, but it'll do for now)
			// nested grid
			foreach ( $field->inputs as $i => $input ) {
				$input_name     = 'input_' . $input['id'];
				$input_gf_attrs = mp_get_attributes( $field_content, "//input[@name='{$input_name}']", true );

				// breakpoint at which the child/master labels are toggled (Name vs. First Name/Last Name)
				$label_breakpoint = 'm';

				// Wrap inputs and move labels to first child position
				if ( ! empty( $input_gf_attrs['id'] ) ) {
					$input_id      = $input_gf_attrs['id'];
					$field_content = mp_wrap_element( $field_content, "//span[@id='{$input_id}_container']", 'div' );
					$field_content = mp_move_element( $field_content, "//label[@for='{$input_id}']", "//input[@id='{$input_id}']/ancestor::div[contains(@class,'{$input_id}_container')]", 'insertBefore' );
					// $field_content = mp_html_class( $field_content, "//label[@for='{$input_id}']", 'uk-hidden@' . $label_breakpoint, true );
				}

				// Hide master label on small screens
				$field_content = mp_html_attrs_by_class( $field_content, '.gfield_label', array( 'hidden' ), true );

				// Placeholder, or it will just use Name because that is the parent field.
				if ( ! empty( $input['customLabel'] ) ) {
					// Prefer label text to support custom labels.
					$placeholder = $input['customLabel'];
				} elseif ( ! empty( $input['label'] ) ) {
					// Fall back on default label.
					$placeholder = $input['label'];
				} else {
					// If no default label, look for aria-label attribute.
					preg_match_all( '/name="' . $input_name . '" [^>]* aria-label="([^"]+)/', $field_content, $matches );
					if ( ! empty( array_filter( $matches ) ) ) {
						$placeholder = __( reset( $matches[1] ), 'text_domain' );
					}
				}

				if ( ! empty( $placeholder ) ) {
					// Override some of the default labels.
					switch ( $placeholder ) {
						case 'First':
							$placeholder = __( 'First name', 'text_domain' );
							break;
						case 'Middle':
							$placeholder = __( 'Middle name(s)', 'text_domain' );
							break;
						case 'Last':
							$placeholder = __( 'Last name', 'text_domain' );
							break;
					}

					// Assign the placeholder, and clear the default placeholder
					$field_content = mp_html_attrs( $field_content, "//input[@name='{$input_name}']", array( 'placeholder' => $placeholder ) );
					unset( $input_attrs['placeholder'] );

					// Hide the label on large screens
					$field_content = mp_html_class( $field_content, "//input[@name='{$input_name}']/parent::*[contains(@id,'_container')]/label", 'uk-hidden@s', true );
				}

				// Set sizes for prefix, first, middle, last, suffix fields
				$name_field_sizes = array(
					'name_prefix' => 'uk-form-width-xsmall',
					'name_first'  => 'uk-width-expand uk-width-large@s uk-width-expand@m',
					'name_middle' => 'uk-width-expand@m',
					'name_last'   => 'uk-width-expand@s',
					'name_suffix' => 'uk-width-small@s',
				);
				foreach ( $name_field_sizes as $name_field => $name_field_size ) {
					$field_content = mp_html_class_by_class( $field_content, "span.{$name_field}", $name_field_size, true );
				}
			}
			break;

		case 'address':
			foreach ( $field->inputs as $input ) {
				$input_name = 'input_' . $input['id'];
				if ( ! empty( $input['label'] ) ) {
					$field_content = mp_html_attrs( $field_content, "//input[@name='{$input_name}']", array( 'placeholder' => $input['label'] ), false );
				}
			}
			break;

		case 'fileupload':
			// Multiple file option gets a drop upload area
			$select_files    = __( 'select files', 'text_domain' );
			$drop_files_here = __( 'Attach files by dropping them here or', 'text_domain' );

			if ( $field->multipleFiles ) {
				$field_content = mp_html_class_by_class( $field_content, 'gform_drop_area', 'uk-placeholder uk-text-center uk-text-muted-darker', true );
				$field_content = mp_html_class_by_class( $field_content, 'gform_button_select_files', 'uk-link', true, 'span' );
				$field_content = preg_replace( '/gform_button_select_files([^>]*>)Select files/', '\1' . $select_files, $field_content );
				$field_content = str_replace( '<span class="gform_drop_instructions">', '<span uk-icon="icon: cloud-upload" class="uk-margin-small-right"></span><span class="gform_drop_instructions">', $field_content );
				$field_content = str_replace( 'Drop files here or', $drop_files_here, $field_content );

				// To change the Preview markup that shows when a file is uploaded, we need to use JavaScript.
				// https://docs.gravityforms.com/gform_file_upload_markup/
				ob_start();
				?>
<script type='text/javascript'>
	gform.addFilter('gform_file_upload_markup', function(html, file, up, strings, imagesUrl) {
		var formId = up.settings.multipart_params.form_id,
			fieldId = up.settings.multipart_params.field_id;
		html = file.name + " <span uk-icon='trash' class='gform_delete uk-link' " +
			"onclick='gformDeleteUploadedFile(" + formId + "," + fieldId + ", this);' " +
			"alt='" + strings.delete_file + "' title='" + strings.delete_file + "' />";

		return html;
	});
</script>
				<?php
				$gform_file_upload_markup = ob_get_clean();
				$field_content            = $field_content . $gform_file_upload_markup;
			} else {
				$field_content    = mp_html_attrs_by_class( $field_content, 'ginput_container_fileupload', array( 'uk-form-custom' => 'target: true' ) );
				$file_input_class = $input_class;
				$file_input_attrs = array(
					'class'       => buildClass( $file_input_class ),
					'type'        => 'text',
					'placeholder' => __( 'Select file', 'text_domain' ),
					'disabled',
				);
				$field_content    = str_replace( '<span class="gform_fileupload_rules', buildAttributes( $file_input_attrs, 'input' ) . '<span class="gform_fileupload_rules', $field_content );
			}
			break;
	}

	// Add a pattern to inputs so we can check if user has typed something into it (:invalid until they do)
	if ( empty( $field->inputMask ) ) {
		$input_attrs['pattern'] = '.*\S.*';
	}

	$input_attrs['class'] = buildClass( $input_class );

	// Target these fields more broadly, so that we get any that can't be targetted by classname.
	$field_content = mp_html_attrs( $field_content, "//*[(self::input or self::textarea) and starts-with(@name,'input_')]", $input_attrs, true );
	$field_content = mp_html_class( $field_content, "//select[starts-with(@name,'input_')]", $input_attrs['class'], true );
	$field_content = mp_html_class( $field_content, "//label[starts-with(@for,'input_') or starts-with(@for,'gform_')]", 'uk-form-label', true );

	// Hidden labels are visible to screen readers.
	if ( ! empty( $field->labelPlacement ) && $field->labelPlacement === 'hidden_label' ) {
		$field_content = mp_html_class_by_class( $field_content, 'uk-form-label', 'screen-reader-text', true );
	}

	// Input counters.
	if ( ! empty( $field->maxLength ) ) {
		$ginput_max_chars  = $field->maxLength;
		$ginput_counter    = strlen( $value );
		$input_id          = "input_{$form['id']}_{$field->id}";
		$field_progressbar = buildAttributes(
			array(
				'id'    => "js-progressbar-{$input_id}",
				'class' => 'uk-progress',
				'value' => $ginput_counter,
				'max'   => $ginput_max_chars,
			),
			'progress',
			true
		);
		$field_content     = mp_move_element( $field_content, $field_progressbar, "#{$input_id}" );
		$field_content     = mp_html_class( $field_content, '.ginput_container', 'has-max-length uk-position-relative', true );
	}

	// Remove gf_clear nodes, which add spacing to the bottom of fieldsets/uk-grids
	$field_content = mp_html_remove_by_class( $field_content, 'gf_clear' );

	// pre($field_content);

	return $field_content;
}

// Inputs with maximum length are updated with a 'data-count' attribute, so we can add a progress bar.
add_filter( 'gform_counter_script', 'mp_gform_counter_script', 10, 5 );
function mp_gform_counter_script( $script, $form_id, $input_id, $max_length, $field ) {
	$script = str_replace( '});', "}, function(data){ jQuery('#{$input_id}').attr('data-count', data.input).siblings('progress').attr('value', data.input); });", $script );
	$script = str_replace( "attr('aria-live','polite')", "attr('aria-live','polite').addClass('uk-text-small uk-text-muted-dark uk-visible@s')", $script );
	return $script;
}

/**
 * Filter the actual form string before it is rendered.
 * When using the DOMDocument functions, and targetting a specific form (gform_get_form_filter_{id}),
 * something screws up and Gravity Forms takes a shit when trying to display the form.
 * Either target an element with the form ID in it, or use string replace functions instead.
 */

// Fieldsets and field containers (.gfield) aren't available in the field_content hook, so we need to do them here.
add_filter( 'gform_get_form_filter', 'mp_gform_field_containers', 10, 2 );
function mp_gform_field_containers( $form_string, $form ) {
	if ( is_gf_forms_editor() ) {
		return $form_string;
	}

	// $form_string = mp_html_attrs($form_string, "#gform_fields_1", ['uk-margin' => ''], TRUE);
	$form_string = mp_html_class_by_class( $form_string, 'gform_fields', 'uk-grid uk-grid-small', true );

    if(isset($form['cssClass'])) {
        if ( ! hasClass( 'compact', $form['cssClass'] ) ) {
            // Section titles go full-width.
            $form_string = mp_html_class_by_class( $form_string, 'gsection', '!uk-form-width-large uk-width-1-1', true );
    
            // HTML blocks go full-width.
            $form_string = mp_html_class_by_class( $form_string, 'gfield_html', '!uk-form-width-large uk-width-1-1', true );
        }
    }
	
	// Complex fieldsets should be 100% width, defaults to 'large' ginput_container_name
	$form_string = mp_html_class_by_class( $form_string, 'fieldset.gfield', '!uk-form-width-large uk-width-1-1 uk-fieldset', true );
	$form_string = mp_html_class( $form_string, '//div[contains(concat(" ", normalize-space(@class), " "), " ginput_container_fileupload ")]/ancestor::div[contains(concat(" ", normalize-space(@class), " "), " gfield ")]', '!uk-form-width-large uk-width-1-1', true );

	// If we need to target specific types of complex fieldsets, we can use the line below
	$form_string = mp_html_class( $form_string, '//div[contains(concat(" " ,normalize-space(@class)," "), " ginput_container_name ")]/ancestor::fieldset', '!uk-form-width-large uk-width-1-1', true );

	// CAPTCHAs
	$form_string = mp_html_class(
		$form_string,
		'//div[contains(concat(" " ,normalize-space(@class)," "), " ginput_recaptcha ")]/ancestor::div[contains(concat(" " ,normalize-space(@class)," "), " gfield ")]',
		'!uk-form-width-large uk-width-1-1',
		true
	);
	$form_string = mp_html_class(
		$form_string,
		'//div[contains(concat(" " ,normalize-space(@class)," "), " ginput_recaptcha ")]/ancestor::div[contains(concat(" " ,normalize-space(@class)," "), " gfield ")]/label',
		'screen-reader-text',
		true
	);
	$form_string = mp_html_attrs(
		$form_string,
		'//div[contains(concat(" " ,normalize-space(@class)," "), " ginput_recaptcha ") and not(@data-badge = "inline")]/ancestor::div[contains(concat(" " ,normalize-space(@class)," "), " gfield ")]',
		array(
			'class' => '!uk-form-width-large uk-position-bottom-center uk-padding-remove',
			'style' => 'bottom: -20px; transform: translate(-50%, 100%)',
		),
		true
	);

	// Clear floating labels
	$form_string = mp_html_class( $form_string, '//label[contains(concat(" " ,normalize-space(@class)," "), " uk-form-label ")]/parent::div', 'uk-clearfix', true );

	// Form Description
	$form_string = mp_html_class( $form_string, '.gform_description', 'uk-text-lead', true, 'p' );

	// Fields with input get has_input class on the wrapper.
	$form_string = mp_html_class( $form_string, '//option[@selected]/ancestor::div[contains(concat(" " ,normalize-space(@class)," "), " gfield ")]', 'has_input', true );

	return $form_string;
}

// Add Google reCAPTCHA badge to form footer.
add_filter( 'gform_get_form_filter', 'mp_gform_get_form_filter_recaptcha_badge', 10, 2 );
function mp_gform_get_form_filter_recaptcha_badge( $form_string, $form ) {
	if ( is_admin() ) {
		return $form_string;
	}

	if (
		! class_exists( 'Gravity_Forms\Gravity_Forms_RECAPTCHA\GF_RECAPTCHA' ) ||
		( ! empty( $form['gravityformsrecaptcha'] ) && ! empty( $form['gravityformsrecaptcha']['disable-recaptchav3'] ) && true === $form['gravityformsrecaptcha']['disable-recaptchav3'] )
	) {
		return $form_string;
	}
	ob_start();
	?>
	<div class='grecaptcha-branding uk-margin-auto uk-text-meta uk-text-small uk-margin-top uk-width-4-5@s'>
		This site is protected by reCAPTCHA and the Google
		<a href="https://policies.google.com/privacy" target="_blank" rel="noopener" aria-label="Privacy Policy">Privacy Policy</a> and
		<a href="https://policies.google.com/terms" target="_blank" rel="noopener" aria-label="Terms of Service">Terms of Service</a> apply.
	</div>
	<?php
	$recaptcha_badge = ob_get_clean();

	if ( ! hasClass( 'compact', $form['cssClass'] ) ) {
		$form_string = mp_move_element( $form_string, $recaptcha_badge, '.gform_footer', 'firstChild' );
	} else {
		$form_string .= $recaptcha_badge;
	}

	return $form_string;
}



/**
 * Gravity Wiz // Gravity Forms // Give First Validation Error Focus
 * http://gravitywiz.com/
 *
 * Plugin Name:  Gravity Forms First Error Focus
 * Plugin URI:   https://gravitywiz.com/make-gravity-forms-validation-errors-mobile-friendlyer/
 * Description:  Automatically focus (and scroll) to the first field with a validation error.
 * Author:       Gravity Wiz
 * Version:      1.3
 * Author URI:   http://gravitywiz.com/
 */
add_filter(
	'gform_pre_render',
	function ( $form ) {
		add_filter( 'gform_confirmation_anchor_' . $form['id'], '__return_false' );

		if ( ! has_action( 'wp_footer', 'gw_first_error_focus_script' ) ) {
			add_action( 'wp_footer', 'gw_first_error_focus_script' );
			add_action( 'gform_preview_footer', 'gw_first_error_focus_script' );
		}

		return $form;
	}
);

function gw_first_error_focus_script() {
	?>
<script type="text/javascript">
	if (window['jQuery']) {
		(function($) {
			$(document).on('gform_post_render', function() {
				// AJAX-enabled forms will call gform_post_render again when rendering new pages or validation errors.
				// We need to reset our flag so that we can still do our focus action when the form conditional logic
				// has been re-evaluated.
				window['gwfef'] = false;
				gwFirstErrorFocus();
			});
			$(document).on('gform_post_conditional_logic', function(event, formId, fields, isInit) {
				if (!window['gwfef'] && fields === null && isInit === true) {
					gwFirstErrorFocus();
					window['gwfef'] = true;
				}
			});

			function gwFirstErrorFocus() {
				var $firstError = $('.gfield.gfield_error:first');
				if ($firstError.length > 0) {
					$firstError.find('input, select, textarea').eq(0).focus();

					// Without setTimeout or requestAnimationFrame, window.scroll/window.scrollTo are not taking
					// effect on iOS and Android.
					requestAnimationFrame(function() {
						window.scrollTo(0, $firstError.offset().top);
					});
				}
			}
		})(jQuery);
	}
</script>
	<?php
}

add_filter( 'gform_submit_button', 'mp_gform_submit_button', 10, 2 );
function mp_gform_submit_button( $button, $form ) {
	if ( is_gf_forms_editor() ) {
		return $button;
	}

	$attrs            = array();
	$attrs['class'][] = 'uk-button uk-margin-top';

	if ( ! empty( $form['buttonColor'] ) ) {
		$attrs['class'][] = $form['buttonColor'];
	}
	if ( ! empty( $form['buttonSize'] ) && $form['buttonSize'] !== 'default' ) {
		$attrs['class'][] = $form['buttonSize'];
	}

	if ( ! empty( $form['buttonClass'] ) ) {
		$attrs['class'][] = $form['buttonClass'];
	}

	if ( ! empty( $form['buttonIcon'] ) ) {
		$icon = get_icon( $form['buttonIcon'] );
		if ( ! empty( $icon ) ) {
			$is_icon_after = ( ! empty( $form['buttonIconAfter'] ) && $form['buttonIconAfter'] == true );
			$icon_class    = $is_icon_after ? 'uk-margin-small-left' : 'uk-margin-small-right';
			$icon          = mp_html_class_by_class( $icon, 'uk-icon', $icon_class, true );
			// $attrs['class'][] = 'has-icon';
		}
	}

	$button = mp_html_attrs( $button, '//input[contains(@type,"submit")]', $attrs, true, 'button' );

	if ( ! empty( $icon ) ) {
		$icon_position = $is_icon_after ? 'lastChild' : 'firstChild';
		$button        = mp_move_element( $button . $icon, '//*[contains(@class,"uk-icon")]', '//button[contains(@type,"submit")]', $icon_position );
	}
	return buildAttributes( array( 'class' => 'gform_submit_button_wrapper' ), 'span', $button );
}



/**
 * Gravity Forms Pre-Render
 *
 * @param   Object     $form   The current form to be filtered.
 *
 * @link    https://docs.gravityforms.com/gform_pre_render/
 */

add_filter( 'gform_pre_render', 'mp_gform_form_uikit' );
function mp_gform_form_uikit( $form ) {
	if ( is_gf_forms_editor() ) {
		return $form;
	}

	// Left label placement: uk-form-horizontal
	if ( 'left_label' === $form['labelPlacement'] ) {
		$form['cssClass'] = buildClass( $form['cssClass'], 'uk-form-horizontal' );
	}
	return $form;
}




// $mp_datepicker_init = "
// jQuery(document).ready(function($) {
// gform.addFilter( 'gform_datepicker_options_pre_init', function( optionsObj, formId, fieldId ) {
// Apply to field 101 of form 1 only
// As this is nested form, so reports fieldId as formId and
// leaving fieldId undefined, behave accordingly :)
// if(formId == 101) {
// optionsObj.maxDate = 0;
// }
// return optionsObj;
// } );
// });";
// wp_add_inline_script( 'gform_datepicker_init', $custom_datepicker_init, 'before' );
