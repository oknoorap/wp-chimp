<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wp-chimp.com
 * @since      0.1.0
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/includes
 */

namespace WP_Chimp;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    WP_Chimp
 * @subpackage WP_Chimp/includes
 * @author     Thoriq Firdaus <thoriqoe@gmail.com>
 */
class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    WP_Chimp\Loader $loader Maintains and registers all hooks for the plugin.
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
		$this->load_instances();

		$this->set_locale();

		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_database_hooks();
		$this->define_api_hooks();

		// Enable Gutenberg blocks if WordPress supports it.
		if ( function_exists( 'register_block_type' ) ) {
			$this->define_blocks_hooks();
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Chimp/Loader. Orchestrates the hooks of the plugin.
	 * - WP_Chimp/Languages. Defines internationalization functionality.
	 * - WP_Chimp/Plugin_Admin. Defines all hooks for the admin area.
	 * - WP_Chimp/Plugin_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 0.1.0
	 * @access private
	 */
	private function load_dependencies() {

		/**
		 * Helpers and utility functions of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-loader.php';

		/**
		 * A base WordPress database table class, which facilitates the creation of
		 * and schema changes to individual database tables.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/storage/class-wp-database.php';

		/**
		 * Class and methods to handle the `wp_chimp_mailchimp_list` database.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/storage/class-wp-database-mailchimp-lists.php';

		/**
		 * Class and methods to queue process on storing the MailChimp from the API response
		 * to the database.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/storage/class-mailchimp-lists-process.php';

		/**
		 * Class and methods that handle database query and caching of the MailChimp list.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/storage/class-mailchimp-lists-query.php';

		/**
		 * The class responsible to register the custom API endpoint with the WP-API
		 * that'll retrieve the MailChimp list.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/endpoints/class-rest-mailchimp-lists-controller.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-languages.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-plugin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-plugin-public.php';

		/**
		 * Functions and classes to load Gutenberg "Subscription Form" in the admin,
		 * and the block rendering in the public-facing of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'blocks/class-blocks-form.php';
	}

	private function load_instances() {
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
	private function set_locale() {

		$languages = new Languages();

		$this->loader->add_action( 'plugins_loaded', $languages, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Plugin_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$admin_page = new Admin_Page( $this->get_plugin_name(), $this->get_version() );
		$admin_menu = new Admin_Menu( $this->get_plugin_name(), $this->get_version(), $admin_page );

		$this->loader->add_action( 'admin_menu', $admin_menu, 'register_menu' );
		$this->loader->add_action( 'admin_init', $admin_page, 'register_page' );

		/**
		 * Add the Action link for the plugin in the Plugin list screen.
		 *
		 * !important that the plugin file name is always referring to the plugin main file
		 * in the plugin's root folder instead of the sub-folders in order for the function to work.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/prefixplugin_action_links_plugin_file/
		 */
		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), $admin_page, 'register_action_links', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_public_hooks() {

		$plugin_public = new Plugin_Public( $this->get_plugin_name(), $this->get_version() );

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

		$db_mailchimp_list = new Storage\WP_Database_MailChimp_Lists();

		// Create or Update the database upon plugin activation.
		register_activation_hook( $this->file, [ $db_mailchimp_list, 'maybe_upgrade' ] );

		$this->loader->add_action( 'switch_blog', $db_mailchimp_list, 'switch_blog' );
		$this->loader->add_action( 'admin_init', $db_mailchimp_list, 'maybe_upgrade' );
	}

	/**
	 * Register custom REST API routes of the plugin using WP-API.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_api_hooks() {

		$lists_query   = new Storage\MailChimp_Lists_Query();
		$lists_process = new Storage\MailChimp_Lists_Process();
		$lists_rest    = new Endpoints\REST_MailChimp_Lists_Controller( $this->get_plugin_name(), $this->get_version() );

		$lists_process->register_lists_query( $lists_query );

		$lists_rest->register_lists_process( $lists_process );
		$lists_rest->register_lists_query( $lists_query );

		$this->loader->add_action( 'rest_api_init', $lists_rest, 'register_routes' );
	}

	/**
	 * Register all of the hooks related to the Gutenberg block functionality
	 * of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_blocks_hooks() {

		$blocks_form = new Blocks_Form();

		$this->loader->add_action( 'init', $blocks_form, 'form_block_init' );
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
	 * @return WP_Chimp\Loader Orchestrates the hooks of the plugin.
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
