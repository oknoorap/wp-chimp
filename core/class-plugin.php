<?php
/**
 * Core: Plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package WP_Chimp\Core
 * @since 0.1.0
 */

namespace WP_Chimp\Core;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use Exception;

use WP_Chimp\Admin;
use WP_Chimp\Subscription_Form;
use WP_Chimp\Deps\DrewM\MailChimp\MailChimp;

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
 * @since 0.3.0 Extends the Core\Plugin_Base class.
 */
class Plugin extends Plugin_Base {

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since 0.1.0
	 */
	protected function load_dependencies() {

		$path = plugin_dir_path( $this->file_path );
		$classes = $path . 'packages/Classes/';

		require_once $classes . 'underdev/requirements/underDEV_Requirements.php';
		require_once $classes . 'a5hleyrich/wp-background-processing/classes/wp-async-request.php';
		require_once $classes . 'a5hleyrich/wp-background-processing/classes/wp-background-process.php';

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

		$languages->set_loader( $this->loader );
		$languages->run();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since 0.1.0
	 */
	protected function define_admin_hooks() {

		$admin = new Admin\Admin( $this->plugin_name, $this->version, $this->file_path );
		$admin->set_loader( $this->loader );
		$admin->run();

		$admin_page = new Admin\Partials\Page( $this->plugin_name, $this->version, $this->file_path );

		/**
		 * Add Lists\Query instance to the Admin\Admin_Page to be able to add, get,
		 * or delete lists from the database.
		 */
		$admin_page->set_lists_query( new Lists\Query() );
		$admin_page->set_loader( $this->loader );
		$admin_page->run();

		$admin_menu = new Admin\Partials\Menu( $this->plugin_name, $this->version );
		$admin_menu->set_loader( $this->loader );
		$admin_menu->run();
	}

	/**
	 * Register all of the hooks related to the database functionality
	 * of the plugin.
	 *
	 * @since 0.1.0
	 */
	protected function define_database_hooks() {

		$lists_db = new Lists\Table();
		$lists_db->set_loader( $this->loader );
		$lists_db->run();

		$options = new Options();

		register_activation_hook( $this->file_path, [ $lists_db, 'maybe_upgrade' ] ); // Create or Updatedatabase on activation_path.
		register_activation_hook( $this->file_path, [ $options, 'ensure_options' ] ); // Add option and the default value on activation.
	}

	/**
	 * Register custom REST API routes of the plugin using WP-API.
	 *
	 * @since 0.1.0
	 */
	protected function define_endpoints_hooks() {

		/**
		 * The MailChimp API key.
		 *
		 * @var string
		 */
		$api_key = get_the_option( 'wp_chimp_api_key' );

		if ( ! empty( $api_key ) ) {

			try {
				$mailchimp = new MailChimp( $api_key );

				$lists_query = new Lists\Query();
				$lists_process = new Lists\Process();

				$lists_rest = new Endpoints\REST_Lists_Controller( $this->plugin_name, $this->version );
				$sync_rest = new Endpoints\REST_Sync_Controller( $this->plugin_name, $this->version );

				$lists_rest->set_mailchimp( $mailchimp );
				$sync_rest->set_mailchimp( $mailchimp );

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

				$lists_rest->set_loader( $this->loader );
				$lists_rest->run();

				$sync_rest->set_loader( $this->loader );
				$sync_rest->run();

			} catch ( Exception $e ) {

				// TODO: Should actually do something here.
				unset( $e );
			}
		}
	}

	/**
	 * Register all of the hooks to register the Subscribe Form.
	 *
	 * @since 0.1.0
	 */
	protected function define_subscription_form_hooks() {

		$subscription_form = new Subscription_Form\Subscription_Form( $this->plugin_name, $this->version, $this->file_path );
		$subscription_form->set_loader( $this->loader );
		$subscription_form->run();
	}

	/**
	 * Register the settings state to be used in the JavaScript side of the plugin.
	 *
	 * @since 0.1.0
	 * @since 0.3.0 The Core\Settings class is introduced.
	 */
	protected function define_settings_hooks() {

		$settings = new Settings( $this->plugin_name, $this->version );
		$settings->set_loader( $this->loader );
		$settings->run();
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
}
