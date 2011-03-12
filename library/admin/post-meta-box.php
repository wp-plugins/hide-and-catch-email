<?php
/**
 * Creates the plugin post meta box functionality.
 *
 * @package HideCatchEmail
 * @subpackage Admin
 */

/* Add the post meta box creation function to the 'admin_menu' hook. */
add_action( 'add_meta_boxes', 'hide_create_post_meta_box' );
add_action( 'admin_menu', 'hide_create_post_meta_box' );

/**
 * Creates a meta box on the post (page, other post types) editing screen for allowing the easy input of 
 * commonly-used post metadata.  The function uses the get_post_types() function for grabbing a list of 
 * available post types and adding a new meta box for each post type.
 *
 * @uses get_post_types() Gets an array of post type objects.
 * @uses add_meta_box() Adds a meta box to the post editing screen.
 */
function hide_create_post_meta_box() {
	
	$domain = 'hide-catch-email';

	/* Gets available public post types. */
	$post_types = get_post_types( array( 'public' => true ), 'objects' );

	/* For each available post type, create a meta box on its edit page if it supports '$prefix-post-settings'. */
	foreach ( $post_types as $type ) {
		/* Add the meta box. */
		add_meta_box( "hide-and-catch-email-{$type->name}-meta-box", __( 'Hide &amp; Catch Email Settings', $domain ), 'hide_post_meta_box', $type->name, 'side', 'default' );
	}

	/* Saves the post meta box data. */
	add_action( 'save_post', 'hide_save_post_meta_box', 10, 2 );
}

/**
 * Creates the settings for the post meta box.  
 *
 * @param string $type The post type of the current post in the post editor.
 */
function hide_post_meta_box_args( $type = '' ) {
	
	$domain = 'hide-catch-email';
	$meta = array();

	/* If no post type is given, default to 'post'. */
	if ( empty( $type ) )
		$type = 'post';
	
	$capability = array (
		'Super Admin' 	=> 'manage_network',
		'Administrator' => 'activate_plugins',
		'Editor' 		=> 'moderate_comments',
		'Author' 		=> 'edit_published_posts',
		'Contributor' 	=> 'edit_posts',
		'Subscriber' 	=> 'read',
	);
	
	$meta['hide'] = array( 'name' => '_HACE', 'title' => sprintf( __( 'Hide %s on this post:', $domain ), '<code>the_content</code>' ), 'type' => 'select', 'options' => array( 'false', 'true' ), 'description' => __( 'Set this to true if you want to hide the content with an email catching form.', $domain ) );
	
	$meta['capability'] = array( 'name' => '_HACE_Capability', 'title' => __( 'User Capability:', $domain ), 'type' => 'select', 'options' => $capability, 'use_key_and_value' => true, 'description' => __( 'Do not show the form to user who are logged in with this capability', $domain ) );
	
	$meta['content'] = array( 'name' => '_HACE_Content', 'title' => __( 'Content text:', $domain ), 'type' => 'textarea', 'description' => __( 'This text will show above the form.', $domain ) );

	return $meta;
}

/**
 * Displays the post meta box on the edit post page. The function gets the various metadata elements
 * from the hide_post_meta_box_args() function. It then loops through each item in the array and
 * displays a form element based on the type of setting it should be.
 *
 * @parameter object $object Post object that holds all the post information.
 * @parameter array $box The particular meta box being shown and its information.
 */
function hide_post_meta_box( $object, $box ) {

	$meta_box_options = hide_post_meta_box_args( $object->post_type ); ?>

	<table class="form-table">

		<?php foreach ( $meta_box_options as $option ) {
			if ( function_exists( "hide_post_meta_box_{$option['type']}" ) )
				call_user_func( "hide_post_meta_box_{$option['type']}", $option, get_post_meta( $object->ID, $option['name'], true ) );
		} ?>

	</table><!-- .form-table -->
    
    <script type="text/javascript">
	jQuery(document).ready(
		function($) {
			$('a.frosty').click( function(e) {
				e.preventDefault();
				$('#frosty').slideToggle('slow');
			});
		}
	);
	</script>
    
    <p class="howto" style="text-align:right"><a class="frosty" href="#">Like this plugin?, donate</a></p>
    
    <div id="frosty" style="display:none;text-align:center">
    	<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8F3JJ9ERQBYS">
        	<input type="button" class="primary" value="DONATE" />
        </a>
    </div>

	<input type="hidden" name="<?php echo "hide-and-catch-email_{$object->post_type}_meta_box_nonce"; ?>" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" /><?php
}

/**
 * Outputs a text input box with the given arguments for use with the post meta box.
 *
 * @param array $args 
 * @param string|bool $value Custom field value.
 */
function hide_post_meta_box_text( $args = array(), $value = false ) {
	$name = preg_replace( "/[^A-Za-z_-]/", '-', $args['name'] ); ?>
	<p>
		<label for="<?php echo $name; ?>"><?php echo $args['title']; ?></label>
		<br />
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>" size="30" tabindex="30" style="width: 99%;" />
		<?php if ( !empty( $args['description'] ) ) echo '<br /><span class="howto">' . $args['description'] . '</span>'; ?>
	</p>
	<?php
}

/**
 * Outputs a select box with the given arguments for use with the post meta box.
 *
 * @param array $args
 * @param string|bool $value Custom field value.
 */
function hide_post_meta_box_select( $args = array(), $value = false ) {
	$name = preg_replace( "/[^A-Za-z_-]/", '-', $args['name'] ); ?>
	<p>
		<label for="<?php echo $name; ?>"><?php echo $args['title']; ?></label>
		<?php if ( !empty( $args['sep'] ) ) echo '<br />'; ?>
		<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
			<?php // echo '<option value=""></option>'; ?>
			<?php foreach ( $args['options'] as $option => $val ) { ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( esc_attr( $value ), esc_attr( $val ) ); ?>><?php echo ( !empty( $args['use_key_and_value'] ) ? $option : $val ); ?></option>
			<?php } ?>
		</select>
		<?php if ( !empty( $args['description'] ) ) echo '<br /><span class="howto">' . $args['description'] . '</span>'; ?>
	</p>
	<?php
}

/**
 * Outputs a textarea with the given arguments for use with the post meta box.
 *
 * @param array $args
 * @param string|bool $value Custom field value.
 */
function hide_post_meta_box_textarea( $args = array(), $value = false ) {
	$name = preg_replace( "/[^A-Za-z_-]/", '-', $args['name'] ); ?>
	<p>
		<label for="<?php echo $name; ?>"><?php echo $args['title']; ?></label>
		<br />
		<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="2" tabindex="30" style="width: 99%;"><?php echo esc_html( $value ); ?></textarea>
		<?php if ( !empty( $args['description'] ) ) echo '<br /><span class="howto">' . $args['description'] . '</span>'; ?>
	</p>
	<?php
}

/**
 * Outputs radio inputs with the given arguments for use with the post meta box.
 *
 * @param array $args
 * @param string|bool $value Custom field value.
 */
function hide_post_meta_box_radio( $args = array(), $value = false ) {
	$name  = preg_replace( "/[^A-Za-z_-]/", '-', $args['name'] ); ?>
	<p>
		<?php echo $args['title']; ?>
		<?php foreach ( $args['options'] as $option => $val ) { ?>
			<br />
			<label for="<?php echo $name; ?>"><?php echo $val; ?></label>
			<input type="radio" name="<?php echo $name; ?>" value="<?php echo esc_attr( $val ); ?>" <?php checked( esc_attr( $value ), esc_attr( $val ) ); ?> />
		<?php } ?>
		<?php if ( !empty( $args['description'] ) ) echo '<br /><span class="howto">' . $args['description'] . '</span>'; ?>
	</p>
	<?php
}

/**
 * The function for saving the theme's post meta box settings. It loops through each of the meta box 
 * arguments for that particular post type and adds, updates, or deletes the metadata.
 *
 * @param int $post_id
 */
function hide_save_post_meta_box( $post_id, $post ) {

	/* Verify that the post type supports the meta box and the nonce before preceding. */
	if ( !isset( $_POST["hide-and-catch-email_{$post->post_type}_meta_box_nonce"] ) || !wp_verify_nonce( $_POST["hide-and-catch-email_{$post->post_type}_meta_box_nonce"], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the post meta box arguments. */
	$metadata = hide_post_meta_box_args( $_POST['post_type'] );

	/* Loop through all of post meta box arguments. */
	foreach ( $metadata as $meta ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta['name'], true );

		/* Get the meta value the user input. */
		$new_meta_value = stripslashes( $_POST[ preg_replace( "/[^A-Za-z_-]/", '-', $meta['name'] ) ] );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta['name'], $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta['name'], $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta['name'], $meta_value );
	}
}

?>