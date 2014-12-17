<?php
/*
 * Plugin Name: Hide &amp; Catch Emails
 * Plugin URI: http://austin.passy.co/wordpress-plugins/hide-and-catch-email
 * Description: Hide your content on any page/post/post_type and replace it with an email catching form. Right now the form consists of a name field, email address, comment field, and spam deterant. To use, activate on the post desired within the metabox. <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8F3JJ9ERQBYS">Like this plugin?, donate.</a> :)
 * Version: 0.4
 * Author: Austin Passy
 * Author URI: http://austin.passy.co
 *
 * @copyright 2009 - 2015
 * @author Austin Passy
 * @link http://frostywebdesigns.com/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package HideCatchEmail
 */

add_action( 'plugins_loaded', 'hide_and_catch_email' );

function hide_and_catch_email() {
	global $hide_and_catch_email;
	$hide_and_catch_email = new Hide_And_Catch_Email;
}

class Hide_And_Catch_Email {
	
	function __construct() {		
		add_action( 'init',				array( $this, 'activate' ) );
		add_action( 'init',				array( $this, 'locale' ) );
		add_filter( 'the_content',		array( $this, 'content' ), 1 );
	}
	
	function activate() {
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'library/admin/post-meta-box.php' );
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'library/admin/dashboard.php' );
	}
	
	function locale() {
		load_plugin_textdomain( 'hide-catch-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	function enqueue() {
		global $post;
		
		$url  = plugin_dir_url( __FILE__ );
		
		if ( !is_admin() && ( is_home() || is_singular() ) ) {
			$hide = get_post_meta( $post->ID, '_HACE', true );
			if ( $hide == 'true' ) {
				wp_enqueue_script( 'hide-catch-email', trailingslashit( $url ) . 'library/js/ajax.js', array( 'jquery' ) );
				wp_localize_script( 'hide-catch-email', 'check_email_form',
					array( 
						'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
						'hideNonce'	=> wp_create_nonce( 'hide-catch-email-nonce' ),
						'cookie'		=> md5( esc_url( get_permalink( $post->ID ) ) )
					)
				);
			}
		}
	}
	
	function ajax_form_check() {
		global $post;
		
		$domain = 'hide-catch-email';
		
		$nonce = $_POST['hideNonce'];

		if ( !wp_verify_nonce( $nonce, 'hide-catch-email-nonce' ) )
			die( 'Uh uh uh! You didn&rsquo;t say the magic word!' );
			
		$post_id = $_POST['id'];
		
		if ( isset( $_POST['hide'] ) ) {			
			$msg = array();
			
			if ( trim( $_POST['checking'] ) !== '' ) {	
				$message = array( "code" => "error", "error" => "checking", "message" => __( "Uh uh uh! You didn&rsquo;t say the magic word!", $domain ) );
			} else {
				$message = array( "code" => "success" );
			}
			
			if ( trim( $_POST['contactName'] ) === '' ) {
				$message = array( "code" => "error", "error" => "contactName", "message" => __( "You forgot to enter your name.", $domain ) );
			} else {
				$message = array( "code" => "success" );
			}
			
			if ( trim( $_POST['email'] ) === '' )  {
				$message = array( "code" => "error", "error" => "email", "message" => __( "You forgot to enter your email address.", $domain ) );
			} elseif ( !is_email( $_POST['email'] ) ) {
				$message = array( "code" => "error", "error" => "email", "message" => __( "You entered an invalid email address.", $domain ) );
			} else {
				$message = array( "code" => "success" );
			}
			
			if ( trim( $_POST['comments'] ) === '' ) {
				$message = array( "code" => "error", "error" => "comments", "message" => __( "You forgot to enter your comments.", $domain ) );
			} else {
				$message = array( "code" => "success" );
			}			
		} else {
			$message = array( "code" => "error", "message" => __( "Nothing has been submitted.", $domain ) );
		}
		
		$msg[] = $message;
		header( "Content-Type: application/json" );
		echo json_encode( $msg );
		exit();
	}
	
	function ajax_success() {
		global $post;
		
		$post_id = $_POST['id'];
		$query = new WP_Query( array( 'post_type' => 'any', 'posts_per_page' => '1', 'paged' => get_query_var( 'page' ) ) );
		if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
			$content = get_the_content();
		endwhile; endif;
		
		$arr = array();
		$entry['code'] 		= "success";
		$entry['id']   		= $post_id;
		$entry['content'] 	= $content;
		
		$arr[] = $entry;
		header( "Content-Type: application/json" );
		echo json_encode( $arr );
		exit();
	}
	
	function content( $content = null ) {
		
		$hide			= get_post_meta( get_the_id(), '_HACE', true );
		$capability	= get_post_meta( get_the_id(), '_HACE_Capability', true );
		
		$removed = sprintf( __( '<p class="alert">%sNote: this content is %shidden%s from public view.%s', 'hide-catch-email' ), '<span class="removed">', '<a href="http://austin.passy.co/wordpress-plugins/hide-and-catch-email" title="Users who are not logged in, or don&rsquo;t have the proper capability will see a submition form.">', '</a>', '</span></p>' );
		
		if ( $hide == 'true' ) {
			if ( is_feed() ) 
				return $removed;
			elseif ( is_user_logged_in() && current_user_can( $capability ) )
				return $removed . $content;
			else
				return $this->form( $content );
		}
	
		return $content;
	}
	
	/**
	 * Actual content replacement check
	 * @ref http://stackoverflow.com/questions/528445/is-there-any-way-to-return-html-in-a-php-function-without-building-the-return-v (clean HTML return)
	 */
	function form( $content = null ) {
		global $post;
		
		$domain = 'hide-catch-email';
		
		$hide 			= get_post_meta( $post->ID, '_HACE', true );
		$text 			= get_post_meta( $post->ID, '_HACE_Content', true );
		$capability 	= get_post_meta( $post->ID, '_HACE_Capability', true );
		
		$url 			= get_option( 'siteurl' );
		$emailAdmin	= get_option( 'admin_email' );
		$sitename 		= get_option( 'blogname' );
		$cookie 		= md5( esc_url( get_permalink( $post->ID ) ) ); 
		$cookieName 	= str_replace( array(" ", "=", ",", ";", "\t", "\r", "\n", "\013", "\014"), '', $sitename );
		
		/* Defaults */
		$nameError 	= '';
		$emailError 	= '';
		$commentError 	= '';
		$sendCopy		= false;
		
		/**
		 * @ref http://trevordavis.net/blog/tutorial/wordpress-jquery-contact-form-without-a-plugin/
		 * If the form is submitted
		 */
		if ( isset( $_POST['hide'] ) ) {
		
			// Check to see if the honeypot captcha field was filled in
			if ( trim( $_POST['checking'] ) !== '' ) {
				$captchaError = true;
			} else {
			
				// Check to make sure that the name field is not empty
				if ( trim( $_POST['contactName'] ) === '' ) {
					$nameError = __( 'You forgot to enter your name.', $domain );
					$hasError = true;
				} else {
					$name = trim( $_POST['contactName'] );
				}
				
				// Check to make sure sure that a valid email address is submitted
				if ( trim( $_POST['email'] ) === '' )  {
					$emailError = __( 'You forgot to enter your email address.', $domain );
					$hasError = true;
				// } elseif ( !eregi( "^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim( $_POST['email'] ) ) ) {
				} elseif ( !is_email( $_POST['email'] ) ) {
					$emailError = __( 'You entered an invalid email address.', $domain );
					$hasError = true;
				} else {
					$email = trim( $_POST['email'] );
				}
					
				// Check to make sure comments were entered
				if ( trim( $_POST['comments'] ) === '' ) {
					$commentError = __( 'You forgot to enter your comments.', $domain );
					$hasError = true;
				} else {
					$comments = esc_html( trim( $_POST['comments'] ) );
				}
					
				// If there is no error, send the email
				if ( !isset( $hasError ) ) {
		
					$emailTo = $emailAdmin;
					$subject = sprintf( __( '%s wanted to view you post [%s]', $domain ), $name, $post->ID );
					$sendCopy = isset( $_POST['sendCopy'] ) && $_POST['sendCopy'] == true ? trim( $_POST['sendCopy'] ) : '';
					$body = "From: {$name} \n\nEmail: {$email} \n\nComments: {$comments} \n\ncc: {$sendCopy}";
					$headers = "From: {$name} <{$emailTo}>\r\nReply-To: {$email}";
					
					wp_mail( $emailTo, $subject, $body, $headers );
		
					if ( $sendCopy == true ) {
						$subject = sprintf( __( 'You viewed post [%s] on %s', $domain ), $post->ID, $sitename );
						$headers = "From: {$name} <{$email}>";
						$body = "From: \t{$name} \n\nEmail: \t{$email} \n\nComments: \t{$comments} \n\ncc: \t{$sendCopy} \n\nThe Hide &amp; Catch Email plugin (http://austinpassy.com/wordpress-plugins/hide-and-catch-email) \nby http://frostywebdesigns.com";
						mail( $email, $subject, $body, $headers );
					}
		
					$emailSent = true;
					
					/*
					 * @ref http://www.webcheatsheet.com/php/cookies.php
					 * @ref http://us3.php.net/manual/en/function.setcookie.php
					 * 
					 * header(s) already sent..
					 * @ref http://stackoverflow.com/questions/2829479/semantics-of-setting-cookies-and-redirecting-without-getting-header-error
					 */
					ob_start();
					setcookie( $cookieName, $cookie, time()+(7 * 24 * 60 * 60), "/", esc_url( $url ), 0 );
					ob_get_clean();
					
//					wp_redirect( esc_url( get_permalink( $post->ID ) ) ); exit;
		
				}
			}
		} 
		
		// Where the magic happens
		if ( ( isset( $emailSent ) && $emailSent == true ) || isset( $_COOKIE[$cookieName] ) ) : 
			return $content;	
		else :
			$replace = '';
			
			if ( $text != '' ) 
				$replace .= '<p>' . esc_html( $text ) . '</p>';
				
			$replace .= '<!-- Start of replacement form by Austin Passy -->';
				if ( isset( $hasError ) || isset( $captchaError ) )
					$replace .= '<p class="error">' . __( 'There was an error submitting the form.', $domain ) . '</p>';
			
			//$replace .= $cookie; //Cookie test
			
			$replace .= '<form action="'. esc_url( get_permalink( $post->ID ) ) .'" id="contactForm-' . $post->ID . '" class="hide-catch-email" method="post">';
			
			$replace .= '<div class="forms">';
			$replace .= '<style type="text/css">.error{background:#FFEBE8;border:1px solid #CC0000;margin-right:5px;padding:2px 4px}</style>';
			$replace .= '<p>';
			$replace .= '<label for="contactName">' . __( 'Your Full Name', $domain ) . '</label><br />';
			$replace .= '<input type="text" name="contactName" id="contactName" value="';
			
			if ( isset( $_POST['contactName'] ) ) 
				$replace .= esc_attr( $_POST['contactName'] ); 
			$replace .= '" class="requiredField';
				
			if ( $nameError != '' ) 
				$replace .= ' error';
			$replace .= '" />';
				
			if ( $nameError != '' )
				$replace .= '<span class="error">' . esc_attr( $nameError ) . '</span> ';
			$replace .= '</p>';
			
			$replace .= '<p>';
			$replace .= '<label for="email">' . __( 'Your Email', $domain ) . '</label><br />';
			$replace .= '<input type="text" name="email" id="email" value="';
			
			if ( isset( $_POST['email'] ) )
				$replace .= esc_attr( $_POST['email'] );
			$replace .= '" class="requiredField email';
			
			if ( $emailError != '' )
				$replace .= ' error';
			$replace .= '" />';
				
			if ( $emailError != '' )
				$replace .= '<span class="error">' . esc_attr( $emailError ) . '</span>';
			$replace .= '</p>';
			
			$replace .= '<p class="textarea">';
			$replace .= '<label for="commentsText">' . __( 'Comments?', $domain ) . '</label><br />';
				
			if ( $commentError != '' )
				$replace .= '<span class="error" style="display:inline-block;margin-bottom:5px">' . esc_attr( $commentError ) . '</span><br />';
			$replace .= '<textarea name="comments" id="commentsText" rows="5" cols="55" class="requiredField';
			if ( $commentError != '' ) 
				$replace .= ' error';
			$replace .= '">';
			
			if ( isset( $_POST['comments'] ) )
				esc_html( $_POST['comments'] );
			
			$replace .= '</textarea>';
			$replace .= '</p>';
			
			$replace .= '<p class="inline">';
			$replace .= '<input type="checkbox" name="sendCopy" id="sendCopy" value="true"';
				
			if ( isset( $_POST['sendCopy'] ) && $_POST['sendCopy'] == true )
				$replace .= ' checked="checked"';
			$replace .= ' />';
			$replace .= '<label for="sendCopy">' . __( 'Send a copy of this email to yourself?', $domain ) . '</label></li>';
			
			$replace .= '<p class="screenReader" style="display:none;">';
			$replace .= '<label for="checking" class="screenReader">' . __( 'If you want to submit this form, do not enter anything in this field', $domain ) . '</label>';
			$replace .= '<input type="text" name="checking" id="checking" class="screenReader" value="';
			if ( isset( $_POST['checking'] ) )
				$replace .= esc_attr( $_POST['checking'] );
			$replace .= '" />';
			$replace .= '</p>';
			
			$replace .= '<p class="buttons">';
			$replace .= '<input type="hidden" name="hide" id="hide" value="true" />';
			$replace .= '<input type="hidden" name="post_id" id="post_id" value="' . get_the_ID() . '" />';
			$replace .= '<input type="submit" class="button" value="Submit" />';
			$replace .= '</p>';
			$replace .= '</div>';
			$replace .= '</form>';
			$replace .= '<!-- End of replacement form by Austin Passy -->';
			
			$content = $replace;
			return $content;
		endif;
	}
	
}