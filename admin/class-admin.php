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

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Chimp\Core;
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
	 * Undocumented variable
	 *
	 * @var [type]
	 */
	private $file_path;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @param string $file_path   The plugin file path.
	 */
	public function __construct( $plugin_name, $version, $file_path ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->file_path = $file_path;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();

		if ( 'settings_page_' . $this->plugin_name === $screen->id || 'widgets' === $screen->id ) {
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

			wp_enqueue_style( $this->plugin_name, plugins_url( 'assets/css/admin.css', $this->file_path ), [], $this->version, 'all' );
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
			wp_register_script( $this->plugin_name, plugins_url( 'assets/js/admin.js', $this->file_path ), [ 'jquery', 'wp-api' ], $this->version );

			wp_enqueue_script( $this->plugin_name );
		}
	}

	/**
	 * Function to register translateable strings in the Admin settings page.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_locale_scripts() {

		$screen = get_current_screen();

		if ( 'settings_page_' . $this->plugin_name === $screen->id ) {

			$locale = [
				'no_lists' => __( 'No MailChimp lists found', 'wp-chimp' ),
			];

			wp_localize_script( $this->plugin_name, 'wpChimpL10n', Core\convert_keys_to_camel_case( $locale ) );
		}
	}
}
