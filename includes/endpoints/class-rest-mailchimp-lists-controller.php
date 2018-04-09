<?php
/**
 * A REST controller for
 *
 * @version 1.4.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/admin/partials/abstract
 */

namespace WP_Chimp\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use \WP_Error;
use \WP_REST_Request;
use \WP_REST_Server;
use \WP_REST_Response;
use \WP_REST_Controller;

use \WP_Chimp\Storage;
use \DrewM\MailChimp\MailChimp;


/**
 * The class for registering custom API Routes using WP-API.
 *
 * @since 0.1.0
 */
final class REST_MailChimp_Lists_Controller extends WP_REST_Controller {

	/**
	 * The Plugin class instance.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $plugin_name;

	/**
	 * The plugin version.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $version;

	/**
	 * The plugin API version.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $api_version = 'v1';

	/**
	 * The API unique namespace.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $namespace;

	/**
	 * The REST Base.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $rest_base;

	/**
	 * The MailChimp API key added in the option.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    string
	 */
	protected $mailchimp_api_key;

	/**
	 * The MailChimp_Lists_Query instance
	 *
	 * Used for interact with the {$prefix}chimp_mailchimp_lists table,
	 * such as inserting a new row or updating the existing rows.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    Storage\MailChimp_Lists_Query
	 */
	protected $list_query;

	/**
	 * The class constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of the plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->namespace = $this->get_namespace();
		$this->rest_base = $this->get_rest_base();
	}

	/**
	 * Function ot get the plugin api namespace.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @return string The name space and the version.
	 */
	public function get_namespace() {
		return 'wp-chimp/' . $this->api_version;
	}

	/**
	 * Get REST Base.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_rest_base() {
		return 'lists';
	}

	/**
	 * Function get the MailChimp API key set.
	 *
	 * @since 0.1.0
	 *
	 * @return string The MailChimp API key or an empty string if it has not
	 *                yet been set.
	 */
	public function get_the_mailchimp_api_key() {
		return get_option( 'wp_chimp_mailchimp_api_key', '' );
	}

	/**
	 * Function to check if the data initiliazed
	 *
	 * The function should prevent the API to fetch the data directly from MailChimp.
	 * As the data has been initialized and synced, the wp-cron is the one that
	 * will fetch the data, and the API should fetch the data from the cached
	 * query in the database.
	 *
	 * @since 0.1.0
	 *
	 * @return bool Return true if the data has been initialised,
	 *              otherwise, returns false.
	 */
	public function is_initialized() {
		return (bool) get_option( 'wp_chimp_mailchimp_list_init', 0 );
	}

	/**
	 * Registers a REST API route.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_rest_route/
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function register_routes() {

		/**
		 * Register the '/lists' route.
		 *
		 * This route requires the 'id' parameter that passes
		 * the post ID.
		 *
		 * @example http://wp-chimp.local/wp-json/wp-chimp/v1/lists
		 *
		 * @uses WP_REST_Server
		 */
		register_rest_route( $this->namespace, $this->rest_base, [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => [
					'context' => $this->get_context_param( [ 'default' => 'view' ] ),
				],
			],
		]);
	}

	public function register_lists_query( $query ) {
		$this->lists_query = $query;
	}

	public function register_lists_process( $process ) {
		$this->lists_process = $process;
	}

	/**
	 * Return the '/wp-chimp/v1/lists' route response.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  WP_REST_Request $request The passed parameters in the route.
	 * @return WP_REST_Response
	 */
	public function get_items( $request ) {
		$mailchimp_lists = $this->get_mailchimp_lists();
		if ( $request instanceof WP_REST_Request ) {
			return rest_ensure_response( $mailchimp_lists );
		}
	}

	/**
	 * Check if a given request has access to get a specific item.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return bool
	 */
	public function get_items_permissions_check( $request ) {
		return true;
	}

	/**
	 * Function to get call the API to get list from MailChimp.
	 *
	 * @since 0.1.0
	 * @access private
	 *
	 * @return mixed Return an object if it is successfully retrieved
	 *               the MailChimp list, or an empty array if not.
	 *               It may also return an Exception if the key,
	 *               added is invalid.
	 */
	private function get_mailchimp_lists() {

		$response = [];                                  // Initialize the response with an empty array.
		$api_key  = $this->get_the_mailchimp_api_key();  // Get the MailChimp API key saved.
		$is_init  = $this->is_initialized();             // Check if the list data is initialised.

		if ( ! empty( $api_key ) && false === $is_init ) {

			$lists        = [];
			$api_response = $this->get_mailchimp_remote_lists( $api_key );

			if ( array_key_exists( 'lists', $api_response ) && 0 !== count( $api_response['lists'] ) ) {
				$lists = \WP_Chimp\sort_mailchimp_data( $api_response['lists'] );
			}

			if ( 0 !== count( $lists ) ) {
				foreach ( $lists as $list ) {
					$this->lists_process->push_to_queue( $list );
				}
				$this->lists_process->save()->dispatch();
			}

			$response = $lists;
		} else {
			$response = $this->get_mailchimp_cached_lists();
		}

		return $response;
	}

	/**
	 * Function to get the list from MailChimp API.
	 *
	 * @since 0.1.0
	 * @access private
	 *
	 * @param string $api_key The MailChimp API key.
	 * @return mixed Returns an object of the lists, or an Exception
	 *               if the API key added is invalid.
	 */
	private function get_mailchimp_remote_lists( $api_key ) {

		/**
		 * The MailChimp API wrapper.
		 *
		 * @var \DrewM\MailChimp\MailChimp
		 */
		$mailchimp = new MailChimp( $api_key );
		return $mailchimp->get( 'lists' );
	}

	/**
	 * Function to get the list from the cache in the local cache
	 * in the database.
	 *
	 * @since 0.1.0
	 * @access private
	 *
	 * @return mixed Returns an object of the lists, empty array, or an Exception
	 *               if the API key added is invalid.
	 */
	private function get_mailchimp_cached_lists() {
		return $this->lists_query->query();
	}
}