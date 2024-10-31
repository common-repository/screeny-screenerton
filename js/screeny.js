jQuery( document ).on( 'click', '.clearcacheclass', function() {
	var post_id = jQuery(this).data('id');
	jQuery.ajax({
		url : clearcachescript.ajax_url,
		type : 'post',
		data : {
			action : 'clear_cache',
			post_id : post_id
		},
		success : function( response ) {
			jQuery('#screenythumb').attr( "src",response )
      location.reload();
		}
	});
})