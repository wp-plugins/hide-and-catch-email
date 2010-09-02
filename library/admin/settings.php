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

?>

<!-- Left Sidebar -->
<div id="left" style="float:left; width:66%;">

<div class="postbox open">

<h3>Activation</h3>

<div class="inside">

	<table class="form-table">

    	<tr>

            <th>

            	<label for="<?php echo $data['use']; ?>">Activate:</label> 

            </th>

            <td>

                <input id="<?php echo $data['use']; ?>" name="<?php echo $data['use']; ?>" type="checkbox" <?php if ( $val['use'] ) echo 'checked="checked"'; ?> value="true" /> <a class="question" title="Help &amp; Examples">[?]</a><br />

                <span class="hide">Check this box to activate the shortcode.</span>

            </td>

		</tr>        

    </table>

    

</div>
</div>

<?php if ( $val['hide_ad'] ) : '';

	else : ?>

<div class="postbox ad">

	<h3>

		<script type='text/javascript' src='http://wpads.net/ads/js.php?type=link&align=center&zone=4'></script>

    </h3>

</div>

<?php endif; ?>



<div class="postbox open">

<h3>Text</h3>

<div class="inside">

	<table class="form-table">
		<tr>
            <td colspan="2">
            	<label for="<?php echo $data['text']; ?>">Text:</label><br />
                
                <textarea id="<?php echo $data['text']; ?>" name="<?php echo $data['text']; ?>" cols="60" rows="20" style="width: 99%;"><?php echo wp_specialchars( stripslashes( $val['text'] ), 1, 0, 1 ); ?></textarea>
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide">Enter text here that you'd like to show up before the submition form, note: you can override this setting in the shortcode.</span>
            </td>
   		</tr>
    </table>    

</div>
</div>


</div> <!-- /float:left -->



<!-- Right Sidebar -->

<div style="float:right; width:33%;">

<div class="postbox open">

<h3>About This Plugin</h3>

<div class="inside">

	<table class="form-table">
        <tr>    
            <th style="width:20%;">Description:</th>    
            <td><?php $plugin_data[ 'Short Description' ]; ?></td>    
        </tr>    
        <tr>    
            <th style="width:20%;">Version:</th>    
            <td><strong><?php echo $plugin_data[ 'Version' ]; ?></strong></td>    
        </tr> 
        <tr>    
            <th style="width:20%;">Support:</th>    
            <td><a href="http://wordpress.org/tags/hide-and-catch-email" title="Get support for this plugin" class="external">WordPress support forums.</a></td>    
        </tr>
	</table>

</div>
</div>

<div id="colordock" class="postbox open">

<h3>Quick Save</h3>

<div class="inside">    

    <p class="submit" style="text-align: center;">
        <input type="submit" name="Submit"  class="button-primary" value="Save Changes" />
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
    </p>    	

</div>
</div>

<div class="postbox open">

<h3>Support This Plugin</h3>

<div class="inside">

	<table class="form-table">
        <tr>
            <th style="width:20%;">Donate:</th>
            <td><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8F3JJ9ERQBYS" title="Donate on PayPal" class="external">PayPal</a>.</td>
        </tr>
        <tr>
            <th style="width:20%;">Rate:</th>
            <td><a href="http://www.wordpress.org/extend/plugins/hide-and-catch-email/" title="WordPress.org Rating" class="external">This plugin on WordPress.org</a>.</td>
        </tr>
	</table>

</div>
</div>

<div class="postbox open">

<h3>About The Author</h3>

<div class="inside">

	<ul>
		<li><?php echo $plugin_data[ 'Author' ]; ?>: Freelance web design / developer &amp; WordPress guru. Also head organizer of <a href="http://wordcamp.la">WordCamp.LA</a></li>     

		<li><a href="http://twitter.com/TheFrosty" title="Austin Passy on Twitter" class="external">Follow me on twitter</a>.</li>       

		<li>Need a WP expert? <a href="http://frostywebdesigns.com/" title="Frosty Web Designs" class="external">Hire me</a>.</li>       

	</ul>   

</div>
</div>

<div class="postbox open">

<h3><a href="http://thefrosty.net">TheFrosty Network</a> feeds</h3>

<div id="tab" class="inside">

	<ul class="tabs">    

    	<li class="t1 t"><a>WordCampLA</a></li>

    	<li class="t2 t"><a>Me!</a></li>

    	<li class="t3 t"><a>wpWorkShop</a></li>

    </ul>    

		<?php if ( function_exists( 'thefrosty_network_feed' ) ) thefrosty_network_feed( 'http://2010.wordcamp.la/feed', '1' ); ?>

		<?php if ( function_exists( 'thefrosty_network_feed' ) ) thefrosty_network_feed( 'http://austinpassy.com/feed', '2' );	?>

		<?php if ( function_exists( 'thefrosty_network_feed' ) ) thefrosty_network_feed( 'http://wpworkshop.la/feed', '3' ); ?>    

</div>
</div>

</div> <!-- /float:right -->


<br style="clear:both;" />