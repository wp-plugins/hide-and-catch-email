	
jQuery(document).ready(
	function($) {
		//disallow 'enter' to submit
		$('form.hide-catch-email').submit( function(e) {
			e.preventDefault();
			
			var form = $('form.hide-catch-email').attr('id');
			var ID   = $('form.hide-catch-email').attr('id').replace('contactForm-', '');
			$.post( check_email_form.ajaxurl, {
				action: 'check_email_form',
				id: ID,
				cookie: check_email_form.cookie, //encodeURIComponent(document.cookie),
				hideNonce: check_email_form.hideNonce
				},
				// @ref http://www.wphardcore.com/2010/5-tips-for-using-ajax-in-wordpress/
				function(jsonString) {
					//console.log(jsonString);
					//var resp = $.parseJSON(jsonString);
					var resp = JSON.parse(jsonString);
					if (resp.code == "success") {
						show_message(resp.message, false);
						show_the_content();
					} else {
						show_message(resp.message, true);
					}
				}			 
			);
			return false;
		});
		
		function show_message(msg, is_error) {
			// Get the data from all the fields
			var name 	= $('form.hide-catch-email input[name=contactName]');
			var email 	= $('form.hide-catch-email input[name=email]');
			var comment = $('form.hide-catch-email textarea');
			var reader 	= $('form.hide-catch-email input[name=checking]');
	 
			/**
			 * Simple validation to make sure user entered something
			 * If error found, add hightlight class to the text field
			 */
			if ( is_error && name.val() == '' ) {
				name.addClass( 'error' );
				name.next('span').html(msg);
				return false;
			} else
				name.removeClass( 'error' );
			 
			if ( is_error && email.val() == '' ) {
				email.addClass( 'error' );
				return false;
			} else
				email.removeClass( 'error' );
			 
			if ( is_error && comment.val() == '' ) {
				comment.addClass( 'error' );
				return false;
			} else
				comment.removeClass( 'error' );			 
				
			if ( is_error ) {
				name.append('<span class="error"></span>').html(msg);
				email.append('<span class=""></span>').html(msg);
				comment.prepend('<span class="error"></span>').html(msg);
			} else {
				name.next('span').remove();
				email..next('span').remove();
				comment.next('span').remove();
			}
		}
		
		function show_the_content(data) {
			var form = $('form.hide-catch-email');
			var ID   = $('form.hide-catch-email').attr('id').replace('contactForm-', '');
			//console.log(ID);
			$.post( check_email_form.ajaxurl, {
				action: 'hide_catch_email_success',
				id: ID
				},
				function(data) {
					for (var entry in data) {
						var content = data[entry].content;
						$(form).wrap('<div id="content-replacement"></div>');
						$(form).slideUp('fast').delay(500).remove();
						$('#content-replacement').html(content);
					}
				}
			);
		}
		
	}
);