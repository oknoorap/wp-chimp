<?php
/**
 * The file to defines the REST_Controller class.
 *
 * @package WP_Chimp/Core/Endpoints
 * @since 0.3.0
 */

namespace WP_Chimp\Core\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_REST_Controller;

use WP_Chimp\Core\Lists;
use WP_Chimp\Core\Loader;
use WP_Chimp\Deps\DrewM\MailChimp\MailChimp;

/**
 * Custom base controller for managing and interacting with REST API items in the plugin.
 *
 * @since 0.3.0
 *
 * @property string $namespace
 * @property string $rest_base
 * @property string $plugin_name
 * @property string $version
 * @property DrewM\MailChimp\MailChimp $mailchimp
 * @property WP_Chimp\Core\Loader $loader
 * @property WP_Chimp\Core\Lists\Query $lists_query
 * @property WP_Chimp\Core\Lists\Process $lists_process
 */
abstract class REST_Controller extends WP_REST_Controller {

	/**
	 * API Endpoint namespace.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	protected $namespace;

	/**
	 * The base of this controller's route.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	protected $rest_base;

	/**
	 * The Plugin class instance.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The plugin version.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	protected $version;

	/**
	 * The MailChimp API key added in the option.
	 *
	 * @since 0.3.0
	 * @var DrewM\MailChimp\MailChimp
	 */
	protected $mailchimp;

	/**
	 * The Query instance
	 *
	 * Used for interact with the {$prefix}chimp_lists table,
	 * such as inserting a new row or updating the existing rows.
	 *
	 * @since 0.3.0
	 * @var WP_Chimp\Core\Lists\Query;
	 */
	protected $lists_query;

	/**
	 * The Process instance
	 *
	 * The Lists\Process instance is extending the WP_Background_Processing class abstraction
	 * enabling asynchronous background processing to add the lists from the MailChimp API
	 * to the database.
	 *
	 * @link https://github.com/A5hleyRich/wp-background-processing WP_Background_Processing repository
	 * @see WP_Background_Process
	 *
	 * @since 0.3.0
	 * @var WP_Chimp\Core\Lists\Process;
	 */
	protected $lists_process;

	/**
	 * The Loader instance.
	 *
	 * @since 0.3.0
	 * @var WP_Chimp\Core\Loader
	 */
	protected $loader;

	/**
	 * The Constructor
	 *
	 * @since 0.3.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->namespace = self::get_namespace();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.3.0
	 */
	abstract public function run();

	/**
	 * Set the loader to orchestrate WordPress Hooks
	 *
	 * @since 0.3.0
	 *
	 * @param Loader $loader The Loader instance.
	 */
	public function set_loader( Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Function to register the MailChimp instance.
	 *
	 * @since 0.3.0
	 *
	 * @param MailChimp $mailchimp The MailChimp instance.
	 */
	public function set_mailchimp( MailChimp $mailchimp ) {
		$this->mailchimp = $mailchimp;
	}

	/**
	 * Register the Lists\Query instance
	 *
	 * @since 0.3.0
	 *
	 * @param Lists\Query $query The List\Query instance to retrieve the lists from the database.
	 */
	public function set_lists_query( Lists\Query $query ) {
		$this->lists_query = $query;
	}

	/**
	 * Register the Lists\Process instance
	 *
	 * @since 0.3.0
	 *
	 * @param Lists\Process $process The Lists\Process instance to add the list on the background.
	 */
	public function set_lists_process( Lists\Process $process ) {
		$this->lists_process = $process;
	}

	/**
	 * Define and retrieve the base of this controller's route.
	 *
	 * @since 0.3.0
	 *
	 * @return string
	 */
	abstract public function get_rest_base();

	/**
	 * Define and retrieve the route namespace.
	 *
	 * @since 0.3.0
	 *
	 * @return string The route namespace.
	 */
	final public static function get_namespace() {
		return 'wp-chimp/' . self::get_rest_version();
	}

	/**
	 * Define and retrieve the REST API version.
	 *
	 * @since 0.3.0
	 *
	 * @return string The version.
	 */
	final public static function get_rest_version() {
		return 'v1';
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 0.3.0
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 0.3.0
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 0.3.0
	 *
	 * @return WP_Chimp\Core\Loader The Loader instance.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
