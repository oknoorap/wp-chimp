<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wp-chimp.com
 * @since      0.1.0
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/admin
 */

namespace WP_Chimp\Admin;

use DrewM\MailChimp\MailChimp;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/admin
 * @author     Thoriq Firdaus <thoriqoe@gmail.com>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    string   $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    string  $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		global $wpdb;

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/class-admin-menu.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/class-admin-page.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();

		if ( 'settings_page_' . $this->plugin_name === $screen->id ) {
			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in WP_Chimp/Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The WP_Chimp/Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', [], $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( 'settings_page_' . $this->plugin_name === $screen->id ) {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in WP_Chimp/Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The WP_Chimp/Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */
			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', [ 'jquery', 'wp-api' ], $this->version );
			wp_localize_script( $this->plugin_name, 'wpChimpLocaleAdmin', self::get_locale_strings() ); // Add translateable strings in the admin page.

			wp_enqueue_script( $this->plugin_name );
		}
	}

	/**
	 * Function to set and get the translatable strings in the admin
	 * area of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 *
	 * @return array The list of translatebale strings.
	 */
	static private function get_locale_strings() {

		return [
			'noLists' => __( 'No MailChimp list found', 'wp-chimp' ),
		];
	}
}
