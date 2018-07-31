<?php
/**
 * Admin: Main class
 *
 * @package WP_Chimp\Admin
 * @since 0.1.0
 */

namespace WP_Chimp\Admin;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Chimp\Core;
use WP_Chimp\Core\Plugin_Base;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since 0.1.0
 * @since 0.3.0 Extends the Core\Plugin_Base class.
 */
class Admin extends Plugin_Base {

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.3.0
	 */
	public function run() {

		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_locale_scripts' );
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
			wp_enqueue_script( 'redom', plugins_url( 'assets/js/redom.min.js', $this->file_path ), [], $this->version, true );
			wp_register_script( $this->plugin_name, plugins_url( 'assets/js/admin.min.js', $this->file_path ), [ 'jquery', 'wp-api' ], $this->version, true );
			wp_enqueue_script( $this->plugin_name );
		}
	}

	/**
	 * Function to register translateable strings in the Admin settings page.
	 *
	 * @since 0.1.0
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
