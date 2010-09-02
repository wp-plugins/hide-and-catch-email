jQuery(document).ready(
	function() {
			
	// Setting the tabs in the sidebar hide and show, setting the current tab
	jQuery('.tab-content').hide();
	jQuery('.t1').show();
	
	jQuery('ul.tabs li.t1 a').addClass('tab-current');
	jQuery('ul li a').css('cursor','pointer');
	
	jQuery('#tab ul.tabs li.t1 a').click(
		function() {
			jQuery('#tab div.tab-content').hide();
			jQuery('ul.tabs li a').removeClass('tab-current');
			jQuery('#tab').find('div.t1').show();
			jQuery(this).addClass('tab-current');			
		}
	);
	jQuery('#tab ul.tabs li.t2 a').click(
		function() {
			jQuery('#tab div.tab-content').hide();
			jQuery('ul.tabs li a').removeClass('tab-current');
			jQuery('#tab').find('div.t2').show();
			jQuery(this).addClass('tab-current');			
		}
	);
	jQuery('#tab ul.tabs li.t3 a').click(
		function() {
			jQuery('#tab div.tab-content').hide();
			jQuery('ul.tabs li a').removeClass('tab-current');
			jQuery('#tab').find('div.t3').show();
			jQuery(this).addClass('tab-current');			
		}
	);
	// #left h3 toggle
	//jQuery('#left .postbox .inside').hide();
	//jQuery('#left .postbox:first .inside').show();
	jQuery('#left .postbox h3').append('<span><abbr title="Click here to hide the box below">click to toggle</abbr></span>');
	jQuery('#left .postbox h3').click(function() {

		jQuery(this).next().toggle(280);

	});
	
	// .ad Block
	jQuery('#left .postbox.ad h3 span').css('display','none');
	
	// #left Warning h3 hide
	jQuery('#left .postbox.warning h3').append('<span class="hide"><abbr title="HIDE!!">Click to remove this box!</abbr></span>');
	jQuery('#left .postbox.warning h3').click(function() {

		jQuery(this).parent().hide();

	});
	
	// #left a.question toggle span.hide
	jQuery('#left  span.hide').hide();
	jQuery('#left a.question').click(function() {
		jQuery(this).next().next().toggleClass('hide').toggleClass('show').toggle(380);
	});
	
	// External window!
	jQuery('a.external').attr('target','_blank');
	if ( jQuery('a.external').attr('title') !== undefined )
		null;
	else
		jQuery(this).attr('title','opens in a new tab');
	
});
