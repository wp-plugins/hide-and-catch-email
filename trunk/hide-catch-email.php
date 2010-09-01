<?php

/*
 * Plugin Name: Hide &amp; Catch Emails
 * Plugin URI: http://austinpassy.com/wordpress-plugins/hide-catch-email
 * Description: Use this simple shortcode to hide any content in your posts/pages. It will then replace the content between the shortcode with a form a user would have to fill out to see said content. Right now the form consists of a name field, email address, comment field, and spam deterant. Use: [replace]xxx[/replace]
 * Version: 0.2
 * Author: Austin Passy
 * Author URI: http://frostywebdesigns.com
 *
 * @copyright 2009 - 2010
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



/**
 * Make sure we get the correct directory.
 * @since 0.1
 */

	if ( !defined( 'WP_CONTENT_URL' ) )
		define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	if ( !defined( 'WP_CONTENT_DIR' ) )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( !defined( 'WP_PLUGIN_URL' ) )
		define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	if ( !defined( 'WP_PLUGIN_DIR' ) )
		define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/**
 * Define constant paths to the plugin folder.
 * @since 0.1
 */

	define( HIDE_CATCH, WP_PLUGIN_DIR . '/hide-catch-email' );
	define( HIDE_CATCH_URL, WP_PLUGIN_URL . '/hide-catch-email' );	

	define( HIDE_CATCH_ADMIN, WP_PLUGIN_DIR . '/hide-catch-email/library/admin' );
	define( HIDE_CATCH_CSS, WP_PLUGIN_URL . '/hide-catch-email/library/css' );
	define( HIDE_CATCH_JS, WP_PLUGIN_URL . '/hide-catch-email/library/js' );

/**
 * Add the settings page to the admin menu.
 * @since 0.1
 */
	add_action( 'admin_init', 'hide_catch_admin_warnings' );
	add_action( 'admin_init', 'hide_catch_admin_init' );
	add_action( 'admin_menu', 'hide_catch_add_pages' );


/**
 * Filters.
 * @since 0.1
 */	
	add_filter( 'plugin_action_links', 'hide_catch_plugin_actions', 10, 2 ); //Add a settings page to the plugin menu

/**
 * Shortcodes.
 * @since 0.1
 */	
	add_shortcode( 'replace', 'hide_catch_content' );

/**
 * Load WP admin files.
 * @since 0.1
 */
	if ( is_admin() ) :
		require_once( HIDE_CATCH_ADMIN . '/settings-admin.php' );
		//require_once( HIDE_CATCH_ADMIN . '/dashboard.php' );
	endif;

/**
 * Load the settings from the database.
 * @since 0.1
 */
	$hide = get_option( 'hide_catch_email_settings' );

 /**
 * Load the stylesheet
 * @since 0.1
 */   
function hide_catch_admin_init() {
	wp_register_style( 'hide-catch-tabs', HIDE_CATCH_CSS . '/tabs.css' );
	wp_register_style( 'hide-catch-admin', HIDE_CATCH_CSS . '/hide-catch-admin.css' );
}

/**
 * Function to add the settings page
 * @since 0.1
 */
function hide_catch_add_pages() {
	if ( function_exists( 'add_options_page' ) ) 
		$page = add_options_page( 'Hide Catch Email Settings', 'Hide Catch Email', 10, 'hide-catch.php', hide_catch_page );
			add_action( 'admin_print_styles-' . $page, 'hide_catch_admin_style' );
			add_action( 'admin_print_scripts-' . $page, 'hide_catch_admin_script' );
}

/**
 * Function to add the style to the settings page
 * @since 0.1
 */
function hide_catch_admin_style() {
	//wp_enqueue_style( 'thickbox' );
	wp_enqueue_style( 'hide-catch-tabs' );
	wp_enqueue_style( 'hide-catch-admin' );
}

/**
 * Function to add the script to the settings page
 * @since 0.1
 */
function hide_catch_admin_script() {
	//wp_enqueue_script( 'thickbox' );
	//wp_enqueue_script( 'theme-preview' );
	wp_enqueue_script( 'hide-catch-reloaded-admin', HIDE_CATCH_JS . '/hide-catch.js', array( 'jquery' ), '0.1', false );
}

function hide_catch_content( $attr, $content = null ) {
	//$content = hide_catch_email_replacement( $content );

	extract( shortcode_atts( array( 
		'capability' 	=> 'level_10',
		'text' 			=> '',
	), $attr ) );

	if ( ( current_user_can( $capability ) && !is_user_logged_in() && !is_null( $content ) ) || is_feed() )
		return $content . '<span class="removed">Note: this content is hidden from public view.</span>';

	//return $content;
//}

/**
 * Actual content replacement check
 * @ref http://stackoverflow.com/questions/528445/is-there-any-way-to-return-html-in-a-php-function-without-building-the-return-v (clean HTML return)
 */
//function hide_catch_email_replacement( $content = null ) {
	global $post,$hide;
	$email = get_option('admin_email');
	$sitename = get_option('blogname');
	$cookie = md5($name); 
	$cookieName = str_replace( array(" ", "=", ",", ";", "\t", "\r", "\n", "\013", "\014"), '', $sitename);
	$desc = get_option('blogdescription');
	
	//@ref http://trevordavis.net/blog/tutorial/wordpress-jquery-contact-form-without-a-plugin/
	//If the form is submitted
	if(isset($_POST['submitted']) ) {
	
		//Check to see if the honeypot captcha field was filled in
		if(trim($_POST['checking']) !== '') {
			$captchaError = true;
		} else {
		
			//Check to make sure that the name field is not empty
			if(trim($_POST['contactName']) === '') {
				$nameError = 'You forgot to enter your name.';
				$hasError = true;
			} else {
				$name = trim($_POST['contactName']);
			}
			
			//Check to make sure sure that a valid email address is submitted
			if(trim($_POST['email']) === '')  {
				$emailError = 'You forgot to enter your email address.';
				$hasError = true;
			} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
				$emailError = 'You entered an invalid email address.';
				$hasError = true;
			} else {
				$email = trim($_POST['email']);
			}
				
			//Check to make sure comments were entered
			if(trim($_POST['comments']) === '') {
				$commentError = 'You forgot to enter your comments.';
				$hasError = true;
			} else {
				if(function_exists('stripslashes')) {
					$comments = stripslashes(trim($_POST['comments']));
				} else {
					$comments = trim($_POST['comments']);
				}
			}
				
			//If there is no error, send the email
			if(!isset($hasError)) {
	
				$emailTo = $email;
				$subject = $name.' wanted to view you post ['.$post->ID.']';
				$sendCopy = trim($_POST['sendCopy']);
				$body = "From: $name \n\nEmail: $email \n\nComments: $comments \n\ncc: $sendCopy";
				$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
				
				mail($emailTo, $subject, $body, $headers);
	
				if($sendCopy == true) {
					$subject = 'You viewed post '.$post->ID.' on '.$sitename;
					$headers = 'From: '.$name.' <'.$email.'>';
					$body = 'From: $name \n\nEmail: $email \n\nComments: $comments \n\ncc: $sendCopy'."\n\n".'<em>the</em> <a href="http://austinpassy.com/wordpress-plugins/hide-catch-email">Hide &amp; Catch Email</a> <em>plugin</em>'."\n".'<em>by</em> <a href="http://frostywebdesigns.com">Frosty Web Designs</a>.';
					mail($email, $subject, $body, $headers);
				}
	
				$emailSent = true;
				/*
				 * @ref http://www.webcheatsheet.com/php/cookies.php
				 * @ref http://us3.php.net/manual/en/function.setcookie.php
				 */
				setcookie($cookieName, $cookie, time()+(7 * 24 * 60 * 60), "/", $HTTP_HOST, 0);
	
			}
		}
	} 
	
	if( ( isset($emailSent) && $emailSent == true ) || isset($_COOKIE[$cookieName]) ) : 
		return $content;	
	else :
		$replace = '';
		
		if ( $text != '' ) 
			$replace .= '<div style="margin-bottom:18px">'.$text.'</div>';
			
		$replace .= '<!-- Start of replacement form by Austin Passy -->';
			if(isset($hasError) || isset($captchaError)) {
				$replace .= '<p class="error">There was an error submitting the form.<p>';
			}
		
		//$replace .= $cookie; //Cookie test
		
		$replace .= '<form action="'. get_permalink() .'" id="contactForm" method="post">';
		
		$replace .= '<div class="forms">';
		$replace .= '<style type="text/css">.error{background:#FFEBE8;border:1px solid #CC0000;margin-right:5px}</style>';
		$replace .= '<p>';
		$replace .= '<label for="contactName">Your Full Name</label><br />';
		$replace .= '<input type="text" name="contactName" id="contactName" value="';
			if(isset($_POST['contactName'])) 
				$replace .= $_POST['contactName']; 
		$replace .= '" class="requiredField';
			
			if($nameError != '') 
				$replace .= ' error';
		$replace .= '" />';
			
			if($nameError != '') {
				$replace .= '<span class="error">'.$nameError.'</span> ';
			}
		$replace .= '</p>';
		
		$replace .= '<p>';
		$replace .= '<label for="email">Your Email</label><br />';
		$replace .= '<input type="text" name="email" id="email" value="';
			if(isset($_POST['email']))
				$replace .= $_POST['email'];
		$replace .= '" class="requiredField email';
			if($emailError != '')
				$replace .= ' error';
		$replace .= '" />';
			
			if($emailError != '') {
				$replace .= '<span class="error">'.$emailError.'</span>';
			}
		$replace .= '</p>';
		
		$replace .= '<p class="textarea">';
		$replace .= '<label for="commentsText">Comments?</label><br />';
			
			if($commentError != '') {
				$replace .= '<span class="error" style="display:inline-block;margin-bottom:5px">'.$commentError.'</span><br /> ';
			}
		$replace .= '<textarea name="comments" id="commentsText" rows="5" cols="55" class="requiredField';
			if($commentError != '') 
				$replace .= ' error';
		$replace .= '">';
		
			if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { stripslashes($_POST['comments']); } else { $_POST['comments']; } } 
		
		$replace .= '</textarea>';
		$replace .= '</p>';
		
		$replace .= '<p class="inline">';
		$replace .= '<input type="checkbox" name="sendCopy" id="sendCopy" value="true"';
			
			if(isset($_POST['sendCopy']) && $_POST['sendCopy'] == true)
				$replace .= ' checked="checked"';
		$replace .= ' />';
		$replace .= '<label for="sendCopy">Send a copy of this email to yourself?</label></li>';
		
		$replace .= '<p class="screenReader" style="display:none;">';
		$replace .= '<label for="checking" class="screenReader">If you want to submit this form, do not enter anything in this field</label>';
		$replace .= '<input type="text" name="checking" id="checking" class="screenReader" value="';
			if(isset($_POST['checking']))
				$replace .= $_POST['checking'];
		$replace .= '" />';
		$replace .= '</p>';
		
		$replace .= '<p class="buttons">';
		$replace .= '<input type="hidden" name="submitted" id="submitted" value="true" />';
		$replace .= '<input type="submit" class="button" value="Submit" />';
		$replace .= '</p>';
		$replace .= '</div>';
		$replace .= '</form>';
		$replace .= '<!-- End of replacement form by Austin Passy -->';
		
		return $replace;
	endif;
}

/**
 * RSS WPCult Feed
 * @since 0.1
 * @package Admin
 */
if ( !function_exists( 'thefrosty_network_feed' ) ) :
	function thefrosty_network_feed( $attr, $count ) {		

		global $wpdb;		

		include_once( ABSPATH . WPINC . '/rss.php' );		

		$rss = fetch_rss( $attr );		

		$items = array_slice( $rss->items, 0, '3' );		

		echo '<div class="tab-content t' . $count . ' postbox open feed">';		

		echo '<ul>';		

		if ( empty( $items ) ) echo '<li>No items</li>';		

		else		

		foreach ( $items as $item ) : ?>		

		<li>		

		<a href='<?php echo $item[ 'link' ]; ?>' title='<?php echo $item[ 'description' ]; ?>'><?php echo $item[ 'title' ]; ?></a><br /> 		

		<span style="font-size:10px; color:#aaa;"><?php echo date( 'F, j Y', strtotime( $item[ 'pubdate' ] ) ); ?></span>		

		</li>		

		<?php endforeach;		

		echo '</ul>';		

		echo '</div>';		

	}

endif;



/**
 * Plugin Action /Settings on plugins page
 * @since 0.1
 * @package plugin
 */
function hide_catch_plugin_actions( $links, $file ) {
 	if( $file == 'hide-catch-email/hide-catch.php' && function_exists( "admin_url" ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=hide-catch.php' ) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}

/**
 * Warnings
 * @since 0.1
 * @package admin
 */
function hide_catch_admin_warnings() {
	global $hide;		

		function hide_catch_warning() {
			global $hide;

			if ( $hide[ 'use' ] != true )

				echo '<div id="hide-catch-warning" class="updated fade"><p><strong>Hide Catch Email plugin is not configured yet.</strong> Please activate it in the <a href="options-general.php?page=hide-catch.php">options</a> page.</p></div>';

		}

		add_action( 'admin_notices', 'hide_catch_warning' );
		
		/*
		function hide_catch_wrong_settings() {
			global $hide;

			if ( $hide[ 'hide_ad' ] != false )

				echo '<div id="hide-catch-warning" class="updated fade"><p><strong>You&prime;ve just hid the ad.</strong> Thanks for <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7329157" title="Donate on PayPal" class="external">donating</a>!</p></div>';

		}

		add_action( 'admin_notices', 'hide_catch_wrong_settings' );
		*/

return;

}

// What??><?