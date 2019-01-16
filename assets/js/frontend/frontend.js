
(function ( $ ) {
	'use strict';

	$.fn.ever_variation_swatches_form = function () {
		return this.each( function() {
			var variationForm = $( this ),
				clicked       = null,
				selected      = [];

			variationForm
				.addClass( 'ever-swatches-role' )
				.on( 'click', '.swatch', function ( e ) {
					e.preventDefault();

					var fullData   = $( this ),
					selectData     = fullData.closest( '.value' ).find( 'select' ),
					attribute_name = fullData.closest( '.value' ).children(".wc-ever-swatches").attr( "data-attribute_name" ),
					optionValue    = fullData.data( 'value' );

					selectData.trigger( 'focusin' );

					if ( ! selectData.find( 'option[value="' + optionValue + '"]' ).length ) {
						fullData.siblings( '.swatch' ).removeClass( 'selected' );
					
						selectData.val( '' ).change();
						variationForm.trigger( 'ever_no_matching_variations', [fullData] );
						return;
					}
					
					clicked = attribute_name;

					if ( selected.indexOf( attribute_name ) === -1 ) {
						selected.push(attribute_name);
					}

					if ( fullData.hasClass( 'selected' ) ) { 
						 selectData.val( '' );
						 fullData.removeClass( 'selected' );

						delete selected[selected.indexOf(attribute_name)];
					} else {
						fullData.addClass( 'selected' ).siblings( '.selected' ).removeClass( 'selected' );
						selectData.val( optionValue );
					}

					selectData.change();
				} )
				.on( 'click', '.reset_variations', function () {
					$( this ).closest( '.variations_form' ).find( '.swatch.selected' ).removeClass( 'selected' );
					selected = [];
				} )
				.on( 'ever_no_matching_variations', function() {
					window.alert( wc_add_to_cart_variation_params.i18n_no_matching_variations_text );
				} );
		} );
	};

	$( function () {
		$( '.variations_form' ).ever_variation_swatches_form();
		$( document.body ).trigger( 'ever_initialized' );
	} );
})( jQuery );
