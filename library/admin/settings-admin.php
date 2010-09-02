<?php
/**
 * Hide Catch Email administration settings
 * These are the functions that allow users to select options
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package HideCatchEmail
 */

/**
 * Returns an array of all the settings defaults
 * Other admin functions can grab this to use
 *
 * @since 0.1
 */
function hide_catch_settings_args() {
	$settings_arr = array(
		/* Use CSS */
		'use' => false,
		'text' => '',		
	);
	
	return $settings_arr;
}

/**
 * Handles the main plugin settings
 *
 * @since 0.1
 */
function hide_catch_page() {
	/*
	* Main settings variables
	*/
	$plugin_name = 'Hide Catch Email';
	$settings_page_title = 'Hide Catch Email Settings';
	$hidden_field_name = 'hide_catch_submit_hidden';
	$plugin_data = get_plugin_data( HIDE_CATCH . '/hide-catch-email.php');

	/*
	* Grabs the default plugin settings
	*/
	$settings_arr = hide_catch_settings_args();

	/*
	* Add a new option to the database
	*/
	add_option( 'hide_catch_email_settings', $settings_arr );

	/*
	* Set form data IDs the same as settings keys
	* Loop through each
	*/
	$settings_keys = array_keys( $settings_arr );
	foreach ( $settings_keys as $key ) :
		$data[$key] = $key;
	endforeach;

	/*
	* Get existing options from database
	*/
	$settings = get_option( 'hide_catch_email_settings' );

	foreach ( $settings_arr as $key => $value ) :
		$val[$key] = $settings[$key];
	endforeach;

	/*
	* If any information has been posted, we need
	* to update the options in the database
	*/
	if ( $_POST[$hidden_field_name] == 'Y' ) :

		/*
		* Loops through each option and sets it if needed
		*/
		foreach ( $settings_arr as $key => $value ) :
			$settings[$key] = $val[$key] = $_POST[$data[$key]];
		endforeach;

		/*
		* Update plugin settings
		*/
		update_option( 'hide_catch_email_settings', $settings );
		
		/*
		* Output the settings page
		*/
        echo '<div class="wrap">';
		if ( function_exists('screen_icon') ) screen_icon();
		echo '<h2>' . $settings_page_title . '</h2>';
		echo '<div class="updated" style="margin:15px 0;">';
		echo '<p><strong>Don&prime;t you feel good. You just saved me!</strong></p>';
		echo '</div>';
		
	
	elseif ( $_POST[$hidden_field_name] == 'R') :

		foreach($settings_arr as $key => $value) :
			$settings[$key] = $val[$key] = $_POST[$data[$key]];
		endforeach;

		delete_option( 'hide_catch_email_settings', $settings );
		
		/*
		* Output the settings page
		*/
        echo '<div class="wrap">';
		if ( function_exists('screen_icon') ) screen_icon();
		echo '<h2>' . $settings_page_title . '</h2>';
		echo '<div class="updated" style="margin:15px 0;">';
		echo '<p><strong>Oh no! I&prime;ve been reset.</strong></p>';
		echo '</div>';

	else :

		echo '<div class="wrap">';
		if ( function_exists('screen_icon') ) screen_icon();
		echo '<h2>' . $settings_page_title . '</h2>';
		
	endif;
?>

			<div id="poststuff">

				<form name="form0" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ); ?>" style="border:none;background:transparent;">

					<?php require_once( HIDE_CATCH_ADMIN . '/settings.php' ); ?>

					<p class="submit" style="float:left;">
						<input type="submit" name="Submit"  class="button-primary" value="Save Changes" />
						<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
					</p>

				</form>
                
                <form name="form0" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="border:none;background:transparent;">
                
                    <p class="submit" style="float:left; margin-left:10px;">
                        <input type="submit" name="Reset" class="swg_warning" value="Delete/Reset" onclick="return confirm('Do you really want to delete/reset the plugin settings?');" />
                        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="R" />
                    </p>
            
                </form>

			</div>
            
			<br style="clear:both;" />

		</div>
<?php
}

?>