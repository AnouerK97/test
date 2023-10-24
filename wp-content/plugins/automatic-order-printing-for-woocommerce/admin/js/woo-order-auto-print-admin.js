(function ( $ ) {
	'use strict';
	$( document ).on(
		'click',
		'a.wocp-admin-action-print',
		function (event) {
			event.preventDefault();

			$( '#woo-order-auto-print-meta' ).block(
				{
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				}
			);

			var $url = $( this ).attr( 'href' );
			$.ajax( $url ).done(
				function (data) {
					$( '#woo-order-auto-print-meta' ).unblock();
					alert( data.data );
				}
			);
		}
	);
})( jQuery );
