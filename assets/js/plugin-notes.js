jQuery( document ).ready( function ( $ ) {

	jQuery( '.pluginnote' ).each( function () {
		var slug = jQuery( this ).attr( 'id' );
		var form = jQuery( this ).find( '#police_comment_div_' + slug );
		var cancel = jQuery( this ).find( '#police_cancel_' + slug );

		jQuery( this ).on( 'click', cancel, function(){
			form.hide();
			jQuery( '#police_comment_link_' + slug ).show();
		});

		var comments = {
			'action': 'pp_dynamic_form',
			'slug':   slug
		};

		jQuery( document ).on( "click", '#police_comment_submit_' + slug, function () {

			var policeComment = {
				'action':  'pp_receive_comment',
				'comment': jQuery( '#police_comment_' + slug ).val(),
				'slug':    slug
			};

			jQuery.post( ajaxurl, policeComment, function ( response ) {
				jQuery( '#' + slug ).html( response );
			} );

			return false;
		} );

		jQuery( document ).on( "click", '#police_comment_link_' + slug, function () {
			form.show();
			jQuery( '.plugin_notes_' + slug ).emojiPicker();
			jQuery( '#police_comment_link_' + slug ).hide();
			jQuery( '#police_comment_' + slug ).focus();
		} );

		jQuery( document ).on( "click", '#plugin_lock_update_' + slug, function () {

			jQuery( '#' + slug ).html( AdminNotes.loading_message );

			var lockUpdates = {
				'action': 'pp_lock_updates',
				'slug':   slug
			};

			jQuery.post( ajaxurl, lockUpdates, function ( response ) {
				jQuery( '#' + slug ).html( response );
			} );
		} );


		jQuery( document ).on( "click", '#plugin_auto_update_' + slug, function () {

			jQuery( '#' + slug ).html( AdminNotes.loading_message );

			var toggleUpdate = {
				'action': 'pp_toggle_updates',
				'slug':   slug
			};

			jQuery.post( ajaxurl, toggleUpdate, function ( response ) {
				jQuery( '#' + slug ).html( response );
			} );
		} );
	} );
} );

