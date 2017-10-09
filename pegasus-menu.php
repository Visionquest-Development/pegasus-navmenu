<?php 
/*
Plugin Name: Pegasus Display Menu Plugin
Plugin URI:  https://developer.wordpress.org/plugins/the-basics/
Description: This allows you to create a navigation menu of items on your website with just a shortcode.
Version:     1.0
Author:      Jim O'Brien
Author URI:  https://visionquestdevelopment.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/




	/*~~~~~~~~~~~~~~~~~~~~
		DISPLAY MENU
	~~~~~~~~~~~~~~~~~~~~~*/
	
	function pegasus_menu_function($atts, $content = null) {
	   extract(
		  shortcode_atts(
			 array( 'name' => null, ),
			 $atts
		  )
	   );
	   return wp_nav_menu(
		  array(
			  'menu' => $name,
			  'echo' => false
			  )
	   );
	}
	add_shortcode('menu', 'pegasus_menu_function');