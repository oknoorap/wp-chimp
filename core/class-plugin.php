<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package WP_Chimp/Core
 * @since 0.1.0
 */

namespace WP_Chimp\Core;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Chimp\Admin;
use WP_Chimp\Subscription_Form;
use WP_Chimp\Packages\DrewM\MailChimp\MailChimp;

/**
 * Loaded dependencies with Mozart.
 *
 * The prefix looks terrible at best, but no other choice at least
 * for the moment.
 *
 * @since 0.2.0
 * @see https://github.com/coenjacobs/mozart
 */
use WP_Chimp_Packages_underDEV_Requirements as WP_Chimp_Requirements;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since 0.1.0
 *
 * @property WP_Chimp\Core\Loader $loader
 * @property string $plugin_name
 * @property string $file_path
 * @property string $version
 */
class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 0.1.0
	 * @var WP_Chimp\Core\Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 0.1.0
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The filename of plugin.
	 *
	 * This is used for WordPress functions requiring the path to the main plugin file,
	 * such as `plugin_dir_path()` and `plugin_basename()`.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $file_path;

	/**
	 * The current version of the plugin.
	 *
	 * @since 0.1.0
	 * @var string $version The current version of the plugin.
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
	 * @param string $file_path   The full path of the main plugin file.
	 */
	public function __construct( $plugin_name, $version, $file_path ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->file_path = $file_path;
	}

	/**
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 0.1.0
	 *
	 * @param Loader $loader The Loader instance.
	 */
	public function set_loader( Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since 0.1.0
	 */
	protected function load_dependencies() {

		$path = plugin_dir_path( $this->file_path );
		$classes = "${path}packages/Classes";

		require_once $classes . '/underdev/requirements/underDEV_Requirements.php';
		require_once $classes . '/a5hleyrich/wp-background-processing/classes/wp-async-request.php';
		require_once $classes . '/a5hleyrich/wp-background-processing/classes/wp-background-process.php';

		require_once $path . 'core/functions.php';
		require_once $path . 'subscription-form/functions.php';
	}

	/**
	 * Check the plugin requirements.
	 *
	 * @since 0.1.0
	 */
	protected function check_requirements() {

		$requires = [
			'php' => '5.4',
			'wp' => '4.9',
			'wp_cron' => true,
		];
		$this->requirements = new WP_Chimp_Requirements( 'WP Chimp', $requires );
		$this->requirements->add_check( 'wp_cron', [ $this, 'check_wp_cron_spawn' ] );
	}

	/**
	 * Check whether the WP-Cron is running.
	 *
	 * @since 0.1.0
	 */
	public function check_wp_cron_spawn() {
		global $wp_version;

		if ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) {
			return;
		}

		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			$notice = __( 'WP-Cron to be running. It is currently disabled using \'DISABLE_WP_CRON\' constant.', 'wp-chimp' );
			$this->requirements->add_error( $notice );
		}
	}

	/**
	 * Define the hooks related to the plugin requirements.
	 *
	 * @since 0.1.0
	 */
	protected function define_requirement_hooks() {
		$this->loader->add_action( 'admin_notices', $this->requirements, 'notice' );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Chimp/Languages class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 0.1.0
	 */
	protected function define_languages_hooks() {

		$languages = new Languages( $this->plugin_name, $this->version, $this->file_path );
		$this->loader->add_action( 'plugins_loaded', $languages, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 0.1.0
	 */
	protected function define_admin_hooks() {

		$admin = new Admin\Admin( $this->plugin_name, $this->version, $this->file_path );
		$admin_page = new Admin\Partials\Page( $this->plugin_name, $this->version );
		$admin_menu = new Admin\Partials\Menu( $this->plugin_name, $this->version );

		/**
		 * Add Lists\Query instance to the Admin\Admin_Page to be able to add, get,
		 * or delete lists from the database.
		 */
		$admin_page->set_lists_query( new Lists\Query() );

		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_locale_scripts' );

		$this->loader->add_action( 'admin_init', $admin_page, 'register_page' );
		$this->loader->add_action( 'added_option', $admin_page, 'added_option', 30, 2 );
		$this->loader->add_action( 'updated_option', $admin_page, 'updated_option', 30, 3 );

		$this->loader->add_action( 'admin_menu', $admin_menu, 'register_menu' );
		$this->loader->add_action( 'current_screen', $admin_menu, 'register_help_tabs' );

		/**
		 * Add the Action link for the plugin in the Plugin list screen.
		 *
		 * !important that_path e plugin file name is always referring to the plugin main file
		 * in the plugin's root folder instead of the sub-folders in order for the function_path to work.
		 *
		 * @link https://developer.wordpress.org/reference/hooks/prefixplugin_action_links_plugin_file/
		 */
		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( $this->file_path ), $admin_page, 'add_action_links', 2 );
	}

	/**
	 * Register all of the hooks related to the database functionality
	 * of the plugin.
	 *
	 * @since 0.1.0
	 */
	protected function define_database_hooks() {

		$lists_db = new Lists\Table();

		register_activation_hook( $this->file_path, [ $lists_db, 'maybe_upgrade' ] ); // Create or Updatedatabase on activation_path.
		register_activation_hook( $this->file_path, [ $this, 'ensure_options' ] ); // Add option and the default value on activation.

		$this->loader->add_action( 'switch_blog', $lists_db, 'switch_blog' );
		$this->loader->add_action( 'admin_init', $lists_db, 'maybe_upgrade' );
	}

	/**
	 * Register custom REST API routes of the plugin using WP-API.
	 *
	 * @since 0.1.0
	 */
	protected function define_endpoints_hooks() {

		$lists_query = new Lists\Query();
		$lists_process = new Lists\Process();

		$lists_rest = new Endpoints\REST_Lists_Controller( $this->plugin_name, $this->version );
		$sync_rest = new Endpoints\REST_Sync_Controller( $this->plugin_name, $this->version );

		/**
		 * The MailChimp API key.
		 *
		 * @var string
		 */
		$api_key = (string) get_option( 'wp_chimp_api_key', '' );

		if ( ! empty( $api_key ) ) {

			$mailchimp = new MailChimp( $api_key );

			$lists_rest->set_mailchimp( $mailchimp );
			$sync_rest->set_mailchimp( $mailchimp );
		}

		/**
		 * Add Lists\Query instance to List\Process and Endpoints\REST_Lists_Controller
		 * to be able to add, get, or delete lists from the database.
		 */
		$lists_process->set_lists_query( $lists_query );
		$lists_rest->set_lists_query( $lists_query );
		$sync_rest->set_lists_query( $lists_query );

		/**
		 * Add Lists\Process instance to Endpoints\REST_Lists_Controller
		 * to add background processing when adding lists from the
		 * MailChimp API response.
		 */
		$lists_rest->set_lists_process( $lists_process );
		$sync_rest->set_lists_process( $lists_process );

		$this->loader->add_action( 'rest_api_init', $lists_rest, 'register_routes' ); // Register `/lists` endpoint.
		$this->loader->add_action( 'rest_api_init', $sync_rest, 'register_routes' ); // Register `/lists/sync` endpoint.
	}

	/**
	 * Register all of the hooks to register the Subscribe Form.
	 *
	 * @since 0.1.0
	 */
	protected function define_subscription_form_hooks() {

		$subscription_form = new Subscription_Form\Subscription_Form( $this->plugin_name, $this->version, $this->file_path );

		$this->loader->add_action( 'init', $subscription_form, 'register_scripts' );
		$this->loader->add_action( 'init', $subscription_form, 'register_block' );
		$this->loader->add_action( 'init', $subscription_form, 'register_shortcode' );
		$this->loader->add_action( 'widgets_init', $subscription_form, 'register_widget' );

		$this->loader->add_action( 'wp_enqueue_scripts', $subscription_form, 'enqueue_scripts', 30 );
		$this->loader->add_action( 'wp_enqueue_scripts', $subscription_form, 'enqueue_locale_scripts', 30 );
		$this->loader->add_action( 'admin_enqueue_scripts', $subscription_form, 'admin_enqueue_locale_scripts', 30 );
	}

	/**
	 * Register the settings state to be used in the JavaScript side of the plugin.
	 *
	 * @since 0.1.0
	 */
	protected function define_settings_hooks() {
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_setting_state', 30 );
	}

	/**
	 * Function to add the settings state.
	 *
	 * The settings state will be used in the JavaScript side of the plugin
	 * i.e. whether we should display the 'Subscription Form', request data
	 * to MailChimp API, etc.
	 *
	 * @since 0.1.0
	 * @see ./admin/js/admin.es
	 * @see ./admin/js/utilities.es
	 */
	public function enqueue_setting_state() {

		$state = self::get_setting_state();
		$data = 'var wpChimpSettingState = ' . wp_json_encode( $state );

		wp_add_inline_script( $this->plugin_name, $data, 'before' );
		wp_add_inline_script( 'wp-chimp-subscription-form-editor', $data, 'before' );
	}

	/**
	 * Retrieve options and nonces.
	 *
	 * This data will be primarily consumed in the JavaScript.
	 *
	 * @since 0.1.0
	 * @see enqueue_setting_state
	 *
	 * @return array
	 */
	protected static function get_setting_state() {

		$args = [
			'nonce' => wp_create_nonce( 'wp-chimp-setting' ),
			'wp_rest_nonce' => wp_create_nonce( 'wp_rest' ),
			'rest_api_url' => get_the_rest_api_url(),
			'mailchimp_api_status' => is_mailchimp_api_valid(),
			'lists_total_items' => get_the_lists_total_items(),
			'lists_init' => is_lists_init(),
		];

		return convert_keys_to_camel_case( $args );
	}

	/**
	 * Add option and the default value if it does not exsit
	 *
	 * @since 0.2.0
	 */
	public function ensure_options() {

		$options = [
			'wp_chimp_api_key' => '',
			'wp_chimp_lists_default' => '',
			'wp_chimp_api_key_status' => 'invalid',
			'wp_chimp_lists_total_items' => 0,
			'wp_chimp_lists_init' => 0,
		];

		foreach ( $options as $key => $default_value ) {
			if ( false === get_option( $key ) ) {
				add_option( $key, $default_value );
			}
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.1.0
	 */
	public function run() {

		$this->load_dependencies();
		$this->check_requirements();

		if ( ! $this->requirements->satisfied() ) {
			$this->define_requirement_hooks();
		} else {
			$this->define_settings_hooks();
			$this->define_languages_hooks();
			$this->define_admin_hooks();
			$this->define_database_hooks();
			$this->define_endpoints_hooks();
			$this->define_subscription_form_hooks();
		}

		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 0.1.0
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return WP_Chimp\Core\Loader The Loader instance.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the plugin file path.
	 *
	 * @since 0.1.0
	 *
	 * @return string The plugin file path.
	 */
	public function get_file_path() {
		return $this->file_path;
	}
}
