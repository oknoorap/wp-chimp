<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link    https://wp-chimp.com
 * @since   0.1.0
 * @package WP_Chimp/Includes
 */

namespace WP_Chimp\Includes;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly, abort.
	die( 'No script kiddies please!' );
}

use WP_Chimp\Admin;
use WP_Chimp\Front;
use WP_Chimp\Blocks;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since  0.1.0
 * @author Thoriq Firdaus <thoriqoe@gmail.com>
 */
class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    WP_Chimp\Includes\Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The filename of plugin.
	 *
	 * This might be used for WordPress functions requiring the path to
	 * the main plugin file, such as `plugin_dir_path()` and `plugin_basename()`.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $file;

	/**
	 * The current version of the plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 0.1.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @param string $file        The full path and filename of the main plugin file.
	 */
	public function __construct( $plugin_name, $version, $file ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->file        = $file;

		$this->load_dependencies();

		$this->define_languages_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_database_hooks();
		$this->define_endpoints_hooks();

		if ( function_exists( 'register_block_type' ) ) { // Enable Gutenberg blocks if WordPress supports it.
			$this->define_blocks_hooks();
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since 0.1.0
	 * @access private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/utilities.php'; // Load the helper and utility functions.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php'; // Load the plugin core Functions.

		/**
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 */
		$this->loader = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Chimp/Languages class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_languages_hooks() {

		$languages = new Languages( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'plugins_loaded', $languages, 'load_plugin_textdomain' );
		$this->loader->add_action( 'admin_enqueue_scripts', $languages, 'enqueue_scripts', 30, 3 );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_admin_hooks() {

		$admin      = new Admin\Admin( $this->get_plugin_name(), $this->get_version() );
		$admin_page = new Admin\Partials\Page( $this->get_plugin_name(), $this->get_version() );
		$admin_menu = new Admin\Partials\Menu( $this->get_plugin_name(), $this->get_version() );

		/**
		 * Add Lists\Query instance to the Admin\Admin_Page to be able to add, get,
		 * or delete lists from the database.
		 */
		$admin_page->register_lists_query( new Lists\Query() );

		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_init', $admin_page, 'register_page' );
		$this->loader->add_action( 'updated_option', $admin_page, 'updated_option', 30, 3 );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin_page, 'enqueue_scripts', 30, 3 );

		$this->loader->add_action( 'admin_menu', $admin_menu, 'register_menu' );
		$this->loader->add_action( 'current_screen', $admin_menu, 'add_help_tabs' );

		/**
		 * Add the Action link for the plugin in the Plugin list screen.
		 *
		 * !important that the plugin file name is always referring to the plugin main file
		 * in the plugin's root folder instead of the sub-folders in order for the function to work.
		 *
		 * @link https://developer.wordpress.org/reference/hooks/prefixplugin_action_links_plugin_file/
		 */
		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), $admin_page, 'add_action_links', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_public_hooks() {

		$plugin_public = new Front\Front( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the database functionality
	 * of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_database_hooks() {

		$lists_db = new Lists\Table();

		register_activation_hook( $this->file, [ $lists_db, 'maybe_upgrade' ] ); // Create or Update the database on activation.

		$this->loader->add_action( 'switch_blog', $lists_db, 'switch_blog' );
		$this->loader->add_action( 'admin_init', $lists_db, 'maybe_upgrade' );
	}

	/**
	 * Register custom REST API routes of the plugin using WP-API.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_endpoints_hooks() {

		$lists_query   = new Lists\Query();
		$lists_process = new Lists\Process();
		$lists_rest    = new Endpoints\REST_Lists_Controller( $this->get_plugin_name(), $this->get_version() );

		/**
		 * Add Lists\Query instance to List\Process and Endpoints\REST_Lists_Controller
		 * to be able to add, get, or delete lists from the database.
		 */
		$lists_process->register_lists_query( $lists_query );
		$lists_rest->register_lists_query( $lists_query );

		/**
		 * Add Lists\Process instance to Endpoints\REST_Lists_Controller
		 * to add background processing when adding lists from the
		 * MailChimp API response.
		 */
		$lists_rest->register_lists_process( $lists_process );

		$this->loader->add_action( 'rest_api_init', $lists_rest, 'register_routes' ); // Register the `/lists` API endpoint.
	}

	/**
	 * Register all of the hooks related to the Gutenberg block functionality
	 * of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_blocks_hooks() {

		$blocks_form = new Blocks\Subscribe_Form();

		$this->loader->add_action( 'init', $blocks_form, 'form_block_init' ); // Register the `subscribe-form` blocks to Gutenberg.
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  0.1.0
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  0.1.0
	 * @return WP_Chimp\Includes\Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  0.1.0
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
