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


/*=================================================================================================
* Bootstrap Nav Walker from github copied from:
* https://github.com/wp-bootstrap/wp-bootstrap-navwalker/blob/v4/class-wp-bootstrap-navwalker.php
* ================================================================================================*/
require_once('class-wp-bootstrap-navwalker.php');


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

	/*~~~~~~~~~~~~~~~~~~~~
		BOOTSTRAP MENU
	~~~~~~~~~~~~~~~~~~~~~*/

	function pegasus_generate_nav_menu( $menu_name, $theme_location ) {

		$return_menu = null;

		$check_for_menu = wp_nav_menu(
			array(
				'menu' => $menu_name,
				'menu_class'	=> 'navbar-nav primary-navigation-bar mr-auto ',
				//'menu_id'		=> '',
				'container'		=> false,
				//'container_class'		=> '',
				//'container_id'		=> '',
				//'fallback_cb'		=> '',
				//'before'		=> '',
				//'after'		=> '',
				////'link_before'		=> '',
				//'link_after'		=> '',
				'echo' => false,
				//'depth'		=> '',
				//'walker'		=> '',
				//'theme_location'		=> '',
				//'items_wrap'      => '<ul id="%1$s" class="%2$s nav navbar-nav">%3$s</ul>',
				//'items_spacing'		=> '',

				'fallback_cb'		=> 'WP_Bootstrap_Navwalker::fallback',
				'walker'			=> new WP_Bootstrap_Navwalker()
			)
		);

		$check_for_theme_location = wp_nav_menu(
			array(
				'theme_location' => $theme_location,
				'menu_class'	=> 'navbar-nav primary-navigation-bar mr-auto ',
				//'menu_id'		=> '',
				'container'		=> false,
				//'container_class'		=> '',
				//'container_id'		=> '',
				//'fallback_cb'		=> '',
				//'before'		=> '',
				//'after'		=> '',
				////'link_before'		=> '',
				//'link_after'		=> '',
				'echo' => false,
				//'depth'		=> '',
				//'walker'		=> '',
				//'theme_location'		=> '',
				//'items_wrap'      => '<ul id="%1$s" class="%2$s nav navbar-nav">%3$s</ul>',
				//'items_spacing'		=> '',

				//'fallback_cb'		=> 'WP_Bootstrap_Navwalker::fallback',
				//'walker'			=> new WP_Bootstrap_Navwalker()

				//'depth'				=> 2,
				//'container'			=> 'div',
				//'container_class'	=> 'collapse navbar-collapse',
				//'container_id'		=> 'bs-example-navbar-collapse-1',
				//'menu_class'		=> 'navbar-nav mr-auto' . $additional_classes,
				//'fallback_cb'		=> 'WP_Bootstrap_Navwalker::fallback',
				//'walker'			=> new WP_Bootstrap_Navwalker()
			)
		);

		if( $check_for_menu ) {
			$return_menu = $check_for_menu;
		} else if ( $check_for_theme_location ) {
			$return_menu = $check_for_theme_location;
		}

		return $return_menu;

	}

	/* Shortcode */

	function pegasus_bootstrap_menu_function($atts, $content = null) {

		$a = shortcode_atts( array(
			'menu'				=> null,
			'theme_location'	=> null,
			'additional_classes'=> '',
			'theme_color'		=> '',
			'theme_background'	=> '',
			'container' 		=> '',
			'id' 				=> '',
			'position' 			=> '',
		), $atts );

		$output = '';
		global $pegasus_nav_counter;
		$pegasus_nav_counter = $pegasus_nav_counter ? $pegasus_nav_counter : 0;

		$overwriteBootstrapStyle = false;
		$menu_name = "{$a['menu']}" ? "{$a['menu']}" : null;
		$theme_location = "{$a['theme_location']}" ? "{$a['theme_location']}" : null;
		$additional_classes = "{$a['additional_classes']}" ? "{$a['additional_classes']}" : ''; //none, navbar-expand, navbar-expand-sm, navbar-expand-md, navbar-expand-lg, navbar-expand-xl
		$theme_color = "{$a['theme_color']}" ? "{$a['additional_classes']}" : 'navbar-light'; //navbar-light, navbar-dark
		$menu_background = "{$a['theme_background']}" ? "{$a['theme_background']}" : ''; //bg-light, bg-dark, bg-faded, bg-primary
		$containerChoice = "{$a['container']}" ? "{$a['container']}" : '';
		$id = "{$a['id']}" ? "{$a['id']}" : '';
		$positionClass = "{$a['position']}" ? "{$a['position']}" : ''; //fixed-top, fixed-bottom, sticky-top

		$pieces = explode( " ", $additional_classes );
		$nav_check = array( 'none', 'navbar-expand', 'navbar-expand-sm', 'navbar-expand-md', 'navbar-expand-lg', 'navbar-expand-xl' );

		foreach( $pieces as $value ) {
			if( in_array( $value, $nav_check ) ) {
				$overwriteBootstrapStyle = true;
			}
		}

		$getBootstrapStyleSetting = get_option('select_for_bootstrap_class');

		$outputBootstrapStyle = $overwriteBootstrapStyle ? $additional_classes : $getBootstrapStyleSetting;

		if( true == $overwriteBootstrapStyle ) {
			$outputBootstrapStyle = $additional_classes;
		}

		if ( empty( $id ) || '' == $id ) {
			$id = 'PegasusBootstrap' . $pegasus_nav_counter;
		}

		if( true == $containerChoice || 'true' == $containerChoice ) {
			$containerChoice = 'container';
		} else if ( false == $containerChoice ) {
			$containerChoice = 'container-fluid';
		}

		/*===================================
		 * Start Output
		 ==================================*/
		$output .= '<nav class="navbar '. $positionClass . ' ' . $outputBootstrapStyle  . ' ' . $theme_color . ' ' . $menu_background . ' ">';
			$output .= '<div class=" ' . $containerChoice . ' ">';
				$output .= '<a class="navbar-brand" href="#">Logo</a>';

				$output .= '<button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#' . $id . '" >';
					$output .= '<span class="navbar-toggler-icon"></span>';
				$output .= '</button> ';

				$output .= '<div class="navbar-collapse collapse" id="' . $id . '" >';
					$output .= pegasus_generate_nav_menu( $menu_name, $theme_location );
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</nav>';

		/* ============== End ===============*/
		$pegasus_nav_counter++;

		return $output;
	}
	add_shortcode('bootstrap_menu', 'pegasus_bootstrap_menu_function');



	function pegasus_nav_menu_item() {
		add_menu_page("Nav", "Nav", "manage_options", "pegasus_nav_plugin_options", "pegasus_nav_plugin_settings_page", null, 99);
		add_submenu_page("pegasus_nav_plugin_options", "Shortcode Usage", "Usage", "manage_options", "pegasus_nav_plugin_shortcode_options", "pegasus_nav_plugin_shortcode_settings_page" );
	}
	add_action("admin_menu", "pegasus_nav_menu_item");

	function pegasus_nav_plugin_settings_page() { ?>
		<div class="wrap">
			<h1>Nav</h1>

			<form method="post" action="options.php">
				<?php
				settings_fields("section2");
				do_settings_sections("theme-options2");
				?>
				<ol>
					<li>None - This will keep the toggle button active at all times. It's like having the mobile menu for all screen sizes.</li>
					<li>Navbar Expand - This is used if you want the dekstop view of the naviagtion to stay even on mobile.</li>
					<li>Navbar Small - This will make the menu change to toggle at 575px (Mobile). </li>
					<li>Navbar Medium - This will make the menu change to toggle at 767px (Mobile). </li>
					<li>Navbar Large - This will make the menu change to toggle at 991px (Mobile). </li>
					<li>Navbar X-Large - This will make the menu change to toggle at 1199px (Mobile). </li>
				</ol>

				<?php
				submit_button();
				?>

			</form>

		</div>
		<?php
	}

	function pegasus_nav_plugin_shortcode_settings_page() { ?>
		<div class="wrap pegasus-wrap">
			<h1>Shortcode Usage</h1>

			<p>Menu Usage: <pre>[menu menu="primary"]</pre></p>

			<p>Bootstrap Menu Usage: <pre>[bootstrap_menu menu="primary" additional_classes="test"]</pre></p>
			<p>Bootstrap Menu Usage: <pre>[bootstrap_menu menu="primary" additional_classes="navbar-expand"]</pre></p>

			<p style="color:red;">MAKE SURE YOU DO NOT HAVE ANY RETURNS OR <?php echo htmlspecialchars('<br>'); ?>'s IN YOUR SHORTCODES, OTHERWISE IT WILL NOT WORK CORRECTLY</p>

		</div>
		<?php
	}

	function pegasus_select_bootstrap_style_option() { ?>

		<select id="select_for_bootstrap_class" name="select_for_bootstrap_class">
			<option value="none" <?php selected( get_option('select_for_bootstrap_class'), 'none', 'selected="selected"' ); ?>><?php echo esc_attr( __( 'None' ) ); ?></option>
			<option value="navbar-expand" <?php selected( get_option('select_for_bootstrap_class'), 'navbar-expand', 'selected="selected"' ); ?>><?php echo esc_attr( __( 'Navbar Expand' ) ); ?></option>
			<option value="navbar-expand-sm" <?php selected( get_option('select_for_bootstrap_class'), 'navbar-expand-sm', 'selected="selected"' ); ?>><?php echo esc_attr( __( 'Expand Small' ) ); ?></option>
			<option value="navbar-expand-md" <?php selected( get_option('select_for_bootstrap_class'), 'navbar-expand-md', 'selected="selected"' ); ?>><?php echo esc_attr( __( 'Expand Medium' ) ); ?></option>
			<option value="navbar-expand-lg" <?php selected( get_option('select_for_bootstrap_class'), 'navbar-expand-lg', 'selected="selected"' ); ?>><?php echo esc_attr( __( 'Expand Large' ) ); ?></option>
			<option value="navbar-expand-xl" <?php selected( get_option('select_for_bootstrap_class'), 'navbar-expand-xl', 'selected="selected"' ); ?>><?php echo esc_attr( __( 'Expand X-Large' ) ); ?></option>
		</select>

		<label for="select_for_bootstrap_class"><br/>This will be the style for all the Bootstrap nav menu's generated with a shortcode. <br/>You can override it by adding the correct class to the additional_classes parameter.</label>

		<p>Example: <pre>[bootstrap_menu menu="primary" additional_classes=" navbar-expand-md test-class"]</pre></p>

		<?php
	}

	function display_nav_plugin_panel_fields() {
		add_settings_section("section2", "Shortcode Settings", null, "theme-options2");

		add_settings_field("select_for_bootstrap_class", "Global Bootstrap Nav Type", "pegasus_select_bootstrap_style_option", "theme-options2", "section2");

		register_setting("section2", "select_for_bootstrap_class");
	}
	add_action("admin_init", "display_nav_plugin_panel_fields");


	function pegasus_nav_plugin_styles() {
		//wp_enqueue_style( 'slick-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/slick.css', array(), null, 'all' );
		wp_enqueue_style( 'pageasus-boostrap-dropdownfix-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/dropdown-fix.css', array(), null, 'all' );
	}
	add_action( 'wp_enqueue_scripts', 'pegasus_nav_plugin_styles' );

	/**
	 * Proper way to enqueue JS
	 */
	function pegasus_nav_plugin_js() {
		//wp_enqueue_script( 'slick-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/slick.js', array( 'jquery' ), null, true );
		//wp_enqueue_script( 'match-height-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/matchHeight.js', array( 'jquery' ), null, true );
		//wp_enqueue_script( 'pegasus-nav-plugin-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/plugin.js', array( 'jquery' ), null, true );
	} //end function
	add_action( 'wp_enqueue_scripts', 'pegasus_nav_plugin_js' );
