jQuery( document ).on( 'keyup keypress change blur', '.uk-input, .uk-select, .uk-textarea', function( e ) {
	const self = jQuery( e.target );
	const wrapper = self.closest( "[id$='_field'],[id^='field_'],.form-row" );
	if ( 'focusout' === e.type ) {
		wrapper.removeClass( 'active' );
	}

	// Changing Country when County contains a value sets the value of the select to null, so clear that before we check for a value.
	if ( self.val() === null ) {
		self.val( '' );
	}

	if ( ! self.val() ) {
		wrapper.removeClass( 'has_input' );
	} else {
		wrapper.addClass( 'has_input' );
		if ( 'focusout' !== e.type ) {
			wrapper.addClass( 'active' );
		}
	}

	// Use the data-validate-required attribute to specify inputs that need value before enabling submit button.
	// The value can be a jQuery selector, so multiple fields are supported.
	const validateRequired = self.closest( '*[data-validate-required]' );
	if ( validateRequired.length ) {
		const selector = validateRequired.attr( 'data-validate-required' );
		const elWrapper = jQuery( validateRequired )
			.find( selector )
			.closest( "[id$='_field'],[id^='field_'],.form-row" )
			.not( '.has_input' );
		validateRequired.find( "[type='submit']" )
			.prop( 'disabled', ( 0 !== elWrapper.length ) )
			.attr( 'aria-disabled', ( 0 !== elWrapper.length ) );
	}
} );

// form icon coloring on input focus
jQuery( document ).on( 'focusin focusout', '.uk-input', function( e ) {
	jQuery( this ).prev( '.uk-form-icon' ).not( 'a' ).toggleClass( 'hovered' );
} );

// auto growing textareas, along with some css
jQuery( document ).on( 'input', 'textarea', function( e ) {
	const $this = jQuery( e.target );
	$this.parent().attr( 'data-value', $this.val() );
} );
