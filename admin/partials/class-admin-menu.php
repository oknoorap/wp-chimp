<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wp-chimp.com
 * @since      0.1.0
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/admin/partials
 */

namespace WP_Chimp;

/**
 * Class that register new menu in the Admin area and load the page.
 *
 * @since 0.1.0
 */
class Admin_Menu {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 * @param string     $plugin_name The name of this plugin.
	 * @param string     $version     The version of this plugin.
	 * @param Admin_Page $admin_page  The page attached to the menu.
	 */
	public function __construct( $plugin_name, $version, Admin_Page $admin_page ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->admin_page  = $admin_page;
	}

	/**
	 * Register a new menu page in the Admin.
	 *
	 * @since 0.1.0
	 */
	public function register_menu() {

		$menu_title = __( 'Chimp', 'wp-chimp' );
		$page_title = __( 'Chimp Settings', 'wp-chimp' );

		add_options_page( $page_title, $menu_title, 'manage_options', $this->plugin_name, [ $this->admin_page, 'render_form' ] );
	}
}
