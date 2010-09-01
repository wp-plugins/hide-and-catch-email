window.onload = function() {
	
	jQuery( window ).scroll( function( ){ 

		var scroller_object = jQuery( '#colordock' );
	
		if( document.documentElement.scrollTop >= 299 || window.pageYOffset >= 299 )
		{
			scroller_object.addClass( 'fixed' );
			//scroller_object.remove().appendTo('#dockbottom');
		}
		else if( document.documentElement.scrollTop < 314 || window.pageYOffset < 314 )
		{
			scroller_object.removeClass( 'fixed' );
			//scroller_object.remove().appendTo('#docktop');
		}
	
	} );

}