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
	 * @since    0.1.0
	 * @access   protected
	 * @var      WP_Chimp\Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {

		if ( defined( 'WP_CHIMP_VERSION' ) ) {
			$this->version = WP_CHIMP_VERSION;
		} else {
			$this->version = '0.1.0';
		}
		$this->plugin_name = 'wp-chimp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// Enable Gutenberg blocks if WordPress supports it.
		if ( \function_exists( 'register_block_type' ) ) {
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
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-loader.php';

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

		/**
		 * A base WordPress database table class, which facilitates the creation of
		 * and schema changes to individual database tables.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'abstract/class-wp-db-table.php';

		/**
		 * Class and methods to handle the `wp_chimp_mailchimp_list` database.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'database/class-db-mailchimp-list.php';

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
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Plugin_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$admin_page = new Admin_Page( $this->get_plugin_name(), $this->get_version() );
		$admin_menu = new Admin_Menu( $this->get_plugin_name(), $this->get_version(), $admin_page );

		$this->loader->add_action( 'admin_menu', $admin_menu, 'register_menu' );
		$this->loader->add_action( 'admin_init', $admin_page, 'register_page' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Plugin_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	/**
	 * Register all of the hooks related to the database functionality
	 * of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 */
	private function define_database_hooks() {

		$db_mailchimp_list = new WP_Database_MailChimp_List();

		// Create or Update the database upon plugin activation.
		register_activation_hook( $this->file, [ $db_mailchimp_list, 'maybe_upgrade' ] );

		$this->loader->add_action( 'switch_blog', $db_mailchimp_list, 'switch_blog' );
		$this->loader->add_action( 'admin_init', $db_mailchimp_list, 'maybe_upgrade' );
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
	 * @since    0.1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    WP_Chimp\Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
