jQuery( document ).ready( function ( $ ) {
	jQuery( '.pluginnote' ).each( function () {
		var slug = jQuery( this ).attr( 'id' );

		var comments = {
			'action': 'pp_dynamic_form',
			'slug':   slug
		};

		jQuery.post( ajaxurl, comments, function ( response ) {
			jQuery( '#' + slug ).html( response );
		} );

		jQuery( document ).on( "click", '#police_comment_submit_' + slug , function () {

			var policeComment = {
				'action':  'pp_receive_comment',
				'comment': jQuery( '#police_comment_' + slug ).val(),
				'slug':    slug
			};

			jQuery.post( ajaxurl, policeComment, function ( response ) {
				jQuery( '#' + slug  ).html( response );
			} );

			return false;
		} );

		jQuery( document ).on( "click", '#police_comment_link_' + slug, function () {
			jQuery( '#police_comment_div_' + slug ).show();
			jQuery( '#police_comment_link_' + slug ).hide();
		} );

	} );

} );