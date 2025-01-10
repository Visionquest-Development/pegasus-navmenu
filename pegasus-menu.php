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

	function pegasus_navmenu_admin_table_css() {
		if ( pegasus_navmenu_check_main_theme_name() == 'Pegasus' || pegasus_navmenu_check_main_theme_name() == 'Pegasus Child' ) {
			//do nothing
		} else {
			//wp_register_style('navmenu-admin-table-css', trailingslashit(plugin_dir_url(__FILE__)) . 'css/pegasus-navmenu-admin-table.css', array(), null, 'all');
			ob_start();
			?>
				pre {
					background-color: #f9f9f9;
					border: 1px solid #aaa;
					page-break-inside: avoid;
					font-family: monospace;
					font-size: 15px;
					line-height: 1.6;
					margin-bottom: 1.6em;
					max-width: 100%;
					overflow: auto;
					padding: 1em 1.5em;
					display: block;
					word-wrap: break-word;
				}
				input[type="text"].code {
					width: 100%;
				}
				table.pegasus-table {
					width: 100%;
					border-collapse: collapse;
					border-color: #777 !important;
				}
				table.pegasus-table th {
					background-color: #f1f1f1;
					text-align: left;
				}
				table.pegasus-table th,
				table.pegasus-table td {
					border: 1px solid #ddd;
					padding: 8px;
				}
				table.pegasus-table tr:nth-child(even) {
					background-color: #f2f2f2;
				}
				table.pegasus-table thead tr { background-color: #282828; }
				table.pegasus-table thead tr td { padding: 10px; }
				table.pegasus-table thead tr td strong { color: white; }
				table.pegasus-table tbody tr:nth-child(0) { background-color: #cccccc; }
				table.pegasus-table tbody tr td { padding: 10px; }
				table.pegasus-table code { color: #d63384; }

			<?php
			// Get the buffered content
			$inline_css = ob_get_clean();

			wp_register_style('navmenu-admin-table-css', false);
			wp_enqueue_style('navmenu-admin-table-css');

			wp_add_inline_style('navmenu-admin-table-css', $inline_css);
		}
	}

	add_action('admin_enqueue_scripts', 'pegasus_navmenu_admin_table_css');

	function pegasus_navmenu_check_main_theme_name() {
		$current_theme_slug = get_option('stylesheet'); // Slug of the current theme (child theme if used)
		$parent_theme_slug = get_option('template');    // Slug of the parent theme (if a child theme is used)

		//error_log( "current theme slug: " . $current_theme_slug );
		//error_log( "parent theme slug: " . $parent_theme_slug );

		if ( $current_theme_slug == 'pegasus' ) {
			return 'Pegasus';
		} elseif ( $current_theme_slug == 'pegasus-child' ) {
			return 'Pegasus Child';
		} else {
			return 'Not Pegasus';
		}
	}

	function pegasus_navmenu_menu_item() {
		if ( pegasus_navmenu_check_main_theme_name() == 'Pegasus' || pegasus_navmenu_check_main_theme_name() == 'Pegasus Child' ) {
			//do nothing
		} else {
			//echo 'This is NOT the Pegasus theme';
			add_menu_page(
				"Pegasus Navmenu", // Page title
				"Navmenu", // Menu title
				"manage_options", // Capability
				"pegasus_navmenu_plugin_options", // Menu slug
				"pegasus_navmenu_plugin_settings_page", // Callback function
				null, // Icon
				86 // Position in menu
			);

			add_submenu_page(
				"pegasus_navmenu_plugin_options", //parent slug
				"Shortcode Usage", // Menu title
				"Usage", // Menu title
				"manage_options", // Capability
				"pegasus_nav_plugin_shortcode_options", // Menu slug
				"pegasus_nav_plugin_shortcode_settings_page" // Callback function
			);
		}
	}
	add_action("admin_menu", "pegasus_navmenu_menu_item");

	function pegasus_nav_plugin_shortcode_settings_page() { ?>
		<div class="wrap pegasus-wrap">
			<h1>Pegasus Navmenu Usage</h1>

			<div>
				<h3>Pegasus Navmenu Usage 1:</h3>

				<pre >[menu menu="primary"]</pre>

				<input
					type="text"
					readonly
					value="<?php echo esc_html('[menu menu="primary"]'); ?>"
					class="regular-text code"
					id="my-shortcode"
					onClick="this.select();"
				>
			</div>

			<div>
				<h3>Pegasus Navmenu Usage 2:</h3>

				<pre >[bootstrap_menu menu="primary" additional_classes="navbar-expand"]</pre>

				<input
					type="text"
					readonly
					value="<?php echo esc_html('[bootstrap_menu menu="primary" additional_classes="navbar-expand"]'); ?>"
					class="regular-text code"
					id="my-shortcode"
					onClick="this.select();"
				>
			</div>

			<p style="color:red;">MAKE SURE YOU DO NOT HAVE ANY RETURNS OR <?php echo htmlspecialchars('<br>'); ?>'s IN YOUR SHORTCODES, OTHERWISE IT WILL NOT WORK CORRECTLY</p>

			<div>
				<?php echo pegasus_navmenu_settings_table(); ?>
			</div>
		</div>
	<?php
	}

	function pegasus_navmenu_settings_table() {

		$data = json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'settings.json' ), true );

		if (json_last_error() !== JSON_ERROR_NONE) {
			return '<p style="color: red;">Error: Invalid JSON provided.</p>';
		}

		// Start building the HTML
		$html = '<table border="0" cellpadding="1" class="table pegasus-table" align="left">
		<thead>
		<tr style="background-color: #282828;">
		<td <span><strong>Name</strong></span></td>
		<td <span><strong>Attribute</strong></span></td>
		<td <span><strong>Options</strong></span></td>
		<td <span><strong>Description</strong></span></td>
		<td <span><strong>Example</strong></span></td>
		</tr>
		</thead>
		<tbody>';

		// Iterate over the data to populate rows
		if (!empty($data['rows'])) {
			foreach ($data['rows'] as $section) {
				// Add section header
				$html .= '<tr >';
				$html .= '<td colspan="5">';
				$html .= '<span>';
				$html .= '<strong>' . htmlspecialchars($section['section_name']) . '</strong>';
				$html .= '</span>';
				$html .= '</td>';
				$html .= '</tr>';

				// Add rows in the section
				foreach ($section['rows'] as $row) {
					$html .= '<tr>
						<td >' . htmlspecialchars($row['name']) . '</td>
						<td >' . htmlspecialchars($row['attribute']) . '</td>
						<td >' . nl2br(htmlspecialchars($row['options'])) . '</td>
						<td >' . nl2br(htmlspecialchars($row['description'])) . '</td>
						<td ><code>' . htmlspecialchars($row['example']) . '</code></td>
					</tr>';
				}
			}
		}

		$html .= '</tbody></table>';

		// Return the generated HTML
		return $html;
	}


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
				'depth'				=> 6,
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

				'depth'				=> 6,
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
		$menu_name = "{$a['menu']}" ? "{$a['menu']}" : null; //menu="primary"
		$theme_location = "{$a['theme_location']}" ? "{$a['theme_location']}" : null; // theme_location="primary"
		$additional_classes = "{$a['additional_classes']}" ? "{$a['additional_classes']}" : ''; //none, navbar-expand, navbar-expand-sm, navbar-expand-md, navbar-expand-lg, navbar-expand-xl
		$theme_color = "{$a['theme_color']}" ? "{$a['theme_color']}" : 'navbar-light'; //navbar-light, navbar-dark
		$menu_background = "{$a['theme_background']}" ? "{$a['theme_background']}" : ''; //bg-light, bg-dark, bg-faded, bg-primary
		$containerChoice = "{$a['container']}" ? "{$a['container']}" : ''; //container = "true"
		$id = "{$a['id']}" ? "{$a['id']}" : ''; //unique ID
		$positionClass = "{$a['position']}" ? "{$a['position']}" : ''; //fixed-top, fixed-bottom, sticky-top

		// Check if additional classes contains any of the predetermined classes, and if so then overwrite the global setting
		$pieces = explode( " ", $additional_classes );
		$nav_check = array( 'none', 'navbar-expand', 'navbar-expand-sm', 'navbar-expand-md', 'navbar-expand-lg', 'navbar-expand-xl' );
		foreach( $pieces as $value ) {
			if( in_array( $value, $nav_check ) ) { $overwriteBootstrapStyle = true; }
		}
		$getBootstrapStyleSetting = get_option('select_for_bootstrap_class');
		$outputBootstrapStyle = $overwriteBootstrapStyle ? $additional_classes : $getBootstrapStyleSetting;
		if( true === $overwriteBootstrapStyle ) {
			$outputBootstrapStyle = $additional_classes;
		}

		//make the ID unique if one isn't set
		if ( empty( $id ) || '' == $id ) {
			$id = 'PegasusBootstrap' . $pegasus_nav_counter;
		}

		//container choice fixes
		if( true === $containerChoice || 'true' == $containerChoice ) {
			$containerChoice = 'container';
		} else if ( false === $containerChoice || 'false' == $containerChoice ) {
			$containerChoice = 'container-fluid';
		}

		//position fixes
		if( 'fixed-top' == $positionClass ) {
			$output .= '<div class="" style="padding-top: 4.5rem;"></div>';
		}
		if( 'sticky-top' == $positionClass ) {
			//$output .= '<div class="" style="padding-top: 9.5rem;"></div>';
			$positionClass = ''; //don't allow for right now
		}

		/*===================================
		 * Start Output
		 ==================================*/

		$output .= '<nav class="navbar '. $positionClass . ' ' . $outputBootstrapStyle  . ' ' . $theme_color . ' ' . $menu_background . ' ">';
			if ( $containerChoice ) { $output .= '<div class=" ' . $containerChoice . ' ">'; }
				$output .= '<a class="navbar-brand" href="#">Logo</a>';

				$output .= '<button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#' . $id . '" >';
					$output .= '<span class="navbar-toggler-icon"></span>';
				$output .= '</button> ';

				$output .= '<div class="navbar-collapse collapse" id="' . $id . '" >';
					$output .= pegasus_generate_nav_menu( $menu_name, $theme_location );
				$output .= '</div>';
			if ( $containerChoice ) { $output .= '</div>'; }
		$output .= '</nav>';

		/* ============== End ===============*/
		$pegasus_nav_counter++;

		return $output;
	}
	add_shortcode('bootstrap_menu', 'pegasus_bootstrap_menu_function');



	// function pegasus_nav_menu_item() {
	// 	add_menu_page("Nav", "Nav", "manage_options", "pegasus_nav_plugin_options", "pegasus_nav_plugin_settings_page", null, 99);
	// 	add_submenu_page("pegasus_nav_plugin_options", "Shortcode Usage", "Usage", "manage_options", "pegasus_nav_plugin_shortcode_options", "pegasus_nav_plugin_shortcode_settings_page" );
	// }
	// add_action("admin_menu", "pegasus_nav_menu_item");

	function pegasus_navmenu_plugin_settings_page() { ?>
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

	/* function pegasus_nav_plugin_shortcode_settings_page() { ?>
		<div class="wrap pegasus-wrap">
			<h1>Shortcode Usage</h1>

			<p>Menu Usage: <pre>[menu menu="primary"]</pre></p>

			<p>Bootstrap Menu Usage: <pre>[bootstrap_menu menu="primary" additional_classes="test"]</pre></p>
			<p>Bootstrap Menu Usage: <pre>[bootstrap_menu menu="primary" additional_classes="navbar-expand"]</pre></p>

			<p style="color:red;">MAKE SURE YOU DO NOT HAVE ANY RETURNS OR <?php echo htmlspecialchars('<br>'); ?>'s IN YOUR SHORTCODES, OTHERWISE IT WILL NOT WORK CORRECTLY</p>

		</div>
		<?php
	} */

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
		wp_enqueue_script( 'pegasus-nav-plugin', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/pegasus-navmenu-plugin.js', array( 'jquery' ), null, true );
	} //end function
	add_action( 'wp_enqueue_scripts', 'pegasus_nav_plugin_js' );
