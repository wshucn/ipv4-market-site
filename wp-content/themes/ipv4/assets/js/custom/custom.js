jQuery( document ).ready( function() {
    var leftElements = document.getElementsByClassName('slide-left');
    for (var i = 0; i < leftElements.length; i++) {
        leftElements[i].setAttribute("uk-scrollspy", "cls: uk-animation-slide-left");
    }
    var rightElements = document.getElementsByClassName('slide-right');
    for (var i = 0; i < rightElements.length; i++) {
        rightElements[i].setAttribute("uk-scrollspy", "cls: uk-animation-slide-right");
    }
    var topElements = document.getElementsByClassName('slide-top');
    for (var i = 0; i < topElements.length; i++) {
        topElements[i].setAttribute("uk-scrollspy", "cls: uk-animation-slide-top");
    }
    var bottomElements = document.getElementsByClassName('slide-bottom');
    for (var i = 0; i < bottomElements.length; i++) {
        bottomElements[i].setAttribute("uk-scrollspy", "cls: uk-animation-slide-bottom");
    }
    var fadeElements = document.getElementsByClassName('fade');
    for (var i = 0; i < fadeElements.length; i++) {
        fadeElements[i].setAttribute("uk-scrollspy", "cls: uk-animation-fade");
    }
    
	// Global active class for elements such as footnotes
	let active;

	// Do stuff when clicking an anchor link
	// jQuery( document ).on( 'click', 'a[href^="#"]:not([href$="#"]):not([aria-expanded])', function( e ) {
	// 	e.preventDefault();
	// 	e.stopPropagation();

	// 	// Get target element ID
	// 	const href = jQuery( this ).attr( 'href' );

	// 	// Add 'active' class to target element
	// 	if ( jQuery( e.target ).is( active ) === false ) {
	// 		jQuery( active ).removeClass( 'active' );
	// 		jQuery( href ).addClass( 'active' );
	// 		active = href;
	// 	}

	// 	// Trigger modals when any link to them is clicked
	// 	const $ukModal = jQuery( href + '[uk-modal]' );
	// 	$ukModal.each( function() {
	// 		UIkit.modal( $ukModal ).show();
	// 	} );
	// } );

	// Remove 'active' class on elements when clicking off of them
	// jQuery( document ).on( 'click', 'body', function( e ) {
	// 	if ( jQuery( e.target ).is( active ) === false ) {
	// 		jQuery( active ).removeClass( 'active' );
	// 		active = false;
	// 	}
	// } );

	jQuery( document ).on( 'change', '.ginput_container_select .uk-select', function( e ) {
		$this = jQuery( e.target );
		$this.attr( 'data-value', jQuery( this ).val() );
	} );

	// scroll to form submit errors
	const error = jQuery( '.gfield_error' ).first();
	if ( error.length > 0 ) {
		active = error.prevAll( '.gform_section' ).length;
		toggle( el, active );

		const errorProduct = error.closest( '.summary' ).prev().find( '.woocommerce-loop-product__title' );
		if ( errorProduct.length > 0 ) {
			errorProduct.trigger( 'click' );
		}
	}
} );

jQuery( window ).on( 'load', function() {
	// Form completion message as modal dialog
	const $confirmation_message = jQuery( '.gform_confirmation_message' ).not( '.uk-modal-dialog .gform_confirmation_message' );
	if ( $confirmation_message.length ) {
		UIkit.modal.dialog( '<div class="uk-modal-body">' + $confirmation_message.html() + '</div>' );
		$confirmation_message.remove();
	}

	// Links to http://page#id show modal dialog with #id
	// Remove the # from the hash, as different browsers may or may not include it
	const hash = location.hash.replace( '#', '' );
	if ( hash != '' ) {
		const $target = jQuery( '#' + hash + '[uk-modal]' );
		$target.each( function() {
			UIkit.modal( this ).show();
		} );
	}

	jQuery( '.ginput_container_select' )
		.find( 'select' )
		.add( '.uk-select' )
		.attr( 'data-value', '' );

	// can't find a way in PHP to have sections or even parent ul pick up
	// the error class of a field
	jQuery( '.gfield_error' )
		.closest( '.gform_fields' )
		.prev()
		.addClass( 'gfield_error' );
} );
