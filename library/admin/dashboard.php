<?php
/** 
 * Function: Register Dashboard Widget
 */
	add_action( 'wp_dashboard_setup', 'cl_register_dashboard_widget' );

if ( !function_exists( 'cl_register_dashboard_widget' ) ) {
	function cl_register_dashboard_widget() {
		wp_add_dashboard_widget('cl_dashboard', __('The Frosty Network <em>feeds</em>'), 'cl_dashboard',
			array(
			'all_link' => 'http://austinpassy.com/',
			'feed_link' => 'http://pipes.yahoo.com/pipes/pipe.run?_id=52c339c010550750e3e64d478b1c96ea&_render=rss',
			'width' => 'half', // OR 'fourth', 'third', 'half', 'full' (Default: 'half')
			'height' => 'double', // OR 'single', 'double' (Default: 'single')
			)
		);
	}
}


// Function: Add Dashboard Widget
	add_filter( 'wp_dashboard_widgets', 'cl_add_dashboard_widget' );
	
if ( !function_exists( 'cl_add_dashboard_widget' ) ) {
	function cl_add_dashboard_widget($widgets) {
		global $wp_registered_widgets;
		if (!isset($wp_registered_widgets['cl_dashboard'])) {
			return $widgets;
		}
		array_splice($widgets, sizeof($widgets)-1, 0, 'cl_dashboard');
		return $widgets;
	}
}


// Function: Print Dashboard Widget
if ( !function_exists( 'cl_dashboard' ) ) {
	function cl_dashboard($sidebar_args) {
		global $wpdb;
		extract( array($sidebar_args, EXTR_SKIP));
		
			include_once( ABSPATH . WPINC . '/class-simplepie.php' );
			$feed = new SimplePie();
			$feed->set_feed_url( 'http://pipes.yahoo.com/pipes/pipe.run?_id=52c339c010550750e3e64d478b1c96ea&_render=rss' );
			$feed->enable_cache( false );
			$feed->init();
			$feed->handle_content_type();
			$feed->set_cache_location( __FILE__ . 'cache' );
	
			$items = $feed->get_item(); ?>
			<div id="dashboard_frosty" class="">
            <style type="text/css">
			#dashboard_frosty h5 {
				display: inline;
				font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
				font-size: 13px !important;
				line-height: 1.8em;
				margin: 0;
			}
			#dashboard_frosty h5 a {
				font-weight: normal;
				line-height: 1.7em;
			}
			</style><?php
			if ( empty( $items ) ) { 
				echo '<h5>No items</h5>';		
			} else {
				foreach( $feed->get_items( 0, 6 ) as $item ) : ?>
                	<p>
                        <h5><a href='<?php echo $item->get_permalink(); ?>' title='<?php echo $item->get_description(); ?>'><?php echo $item->get_title(); ?></a></h5>
                        <span style="font-size:10px; color:#aaa;"><?php echo $item->get_date('F, jS Y'); ?></span>
                    </p>
				<?php endforeach;
			}	
			echo '</div>';		
	}
}

?>