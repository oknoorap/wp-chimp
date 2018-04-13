<?php
/**
 * The file that defines a REST controller for '/lists' endpoints.
 *
 * @link    https://wp-chimp.com
 * @since   0.1.0
 * @package WP_Chimp/Includes
 */

namespace WP_Chimp\Includes\Endpoints;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly, abort.
	die;
}

use WP_Error;
use WP_REST_Request;
use WP_REST_Server;
use WP_REST_Response;
use WP_REST_Controller;

use WP_Chimp\Includes\Lists;
use WP_Chimp\Includes\Utilities;

use DrewM\MailChimp\MailChimp;

/**
 * The class that register the custom '/list' endpoint to WP-API.
 *
 * @since  0.1.0
 * @author Thoriq Firdaus <thoriqoe@gmail.com>
 */
final class REST_Lists_Controller extends WP_REST_Controller {

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
	 * The Query instance
	 *
	 * Used for interact with the {$prefix}chimp_lists table,
	 * such as inserting a new row or updating the existing rows.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    Storage\Query
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
	 * Function to register the Query instance
	 *
	 * @since 0.1.0
	 *
	 * @param Lists\Query $query The List\Query instance to retrieve the lists from the database.
	 * @return void
	 */
	public function register_lists_query( Lists\Query $query ) {
		$this->lists_query = $query;
	}

	/**
	 * Function to register the Lists\Process instance
	 *
	 * The Lists\Process instance is extending the WP_Background_Processing class abstraction
	 * enabling asynchronous background processing to add the lists from the MailChimp API
	 * to the database.
	 *
	 * @link https://github.com/A5hleyRich/wp-background-processing WP_Background_Processing repository
	 * @see WP_Background_Process
	 *
	 * @since 0.1.0
	 *
	 * @param Lists\Process $process The Lists\Process instance to add the list on the background.
	 * @return void
	 */
	public function register_lists_process( Lists\Process $process ) {
		$this->lists_process = $process;
	}

	/**
	 * Registers a custom REST API route.
	 *
	 * @since 0.1.0
	 *
	 * @return void
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
				'args'                => $this->get_collection_params(),
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		]);
	}

	/**
	 * Check if a given request has access to get a specific item.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool
	 */
	public function get_items_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Function to return the response from '/wp-chimp/v1/lists' endpoints.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function get_items( $request ) {

		$lists = $this->get_lists( $request );

		$total_items = $lists['total_items'];
		$total_pages = ceil( $total_items / $this->get_lists_per_page() );

		$items = [];
		foreach ( $lists['lists'] as $key => $list ) {
			$data    = $this->prepare_item_for_response( $list, $request );
			$items[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $items );

		$response->header( 'X-WP-Chimp-Lists-Total', absint( $total_items ) );
		$response->header( 'X-WP-Chimp-Lists-TotalPages', absint( $total_pages ) );

		return $response;
	}

	/**
	 * Prepares a single MailChimp list output for response.
	 *
	 * @since 0.1.0
	 *
	 * @param array           $list The detail of a MailChimp list (e.g. list_id, name, etc.).
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( array $list, $request ) {

		$data   = [];
		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['listId'] ) ) {
			$data['listId'] = wp_strip_all_tags( $list['list_id'], true );
		}

		if ( ! empty( $schema['properties']['name'] ) ) {
			$data['name'] = wp_strip_all_tags( $list['name'], true );
		}

		if ( ! empty( $schema['properties']['subscribers'] ) ) {
			$data['subscribers'] = absint( $list['subscribers'] );
		}

		if ( ! empty( $schema['properties']['doubleOptin'] ) ) {
			$data['doubleOptin'] = absint( $list['double_optin'] );
		}

		return rest_ensure_response( $data ); // Wrap the data in a response object.
	}

	/**
	 * Get the query params for collections
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function get_collection_params() {

		return [
			'page' => [
				'description'       => __( 'Current page of the collection.', 'wp-chimp' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			],
		];
	}

	/**
	 * Retrieves the list's schema, conforming to JSON Schema.
	 *
	 * @since 0.1.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {

		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => __( 'MailChimp Lists', 'wp-chimp' ),
			'type'       => 'object',
			'properties' => [
				'listId'      => [
					'description' => __( 'A string that uniquely identifies this list.', 'wp-chimp' ),
					'type'        => 'string',
					'readonly'    => true,
				],
				'name'        => [
					'description' => __( 'The name of the list.', 'wp-chimp' ),
					'type'        => 'string',
					'readonly'    => true,
					'arg_options' => 'sanitize_text_field',
				],
				'subscribers' => [
					'description' => __( 'The number of active members in the list.', 'wp-chimp' ),
					'type'        => 'integer',
					'readonly'    => true,
					'arg_options' => [
						'sanitize_callback' => 'absint',
					],
				],
				'doubleOptin' => [
					'description' => __( 'Whether or not to require the subscriber to confirm subscription via email.', 'wp-chimp' ),
					'type'        => 'boolean',
					'readonly'    => true,
					'arg_options' => [
						'sanitize_callback' => 'absint',
					],
				],
			],
		];
	}

	/**
	 * Function to get call the API to get list from MailChimp.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return mixed Return an object if it is successfully retrieved the MailChimp list,
	 *               or an empty array if not. It may also return an Exception if
	 *               the key, added is invalid.
	 */
	private function get_lists( $request ) {

		$api_key = $this->get_the_mailchimp_api_key();  // Get the MailChimp API key saved.
		$is_init = $this->is_lists_init();              // Check if the list data is initialised.
		$lists   = [];

		if ( ! empty( $api_key ) && false === $is_init ) {
			$lists = $this->get_remote_lists( $api_key );

			if ( isset( $lists['lists'] ) && 0 !== count( $lists['lists'] ) ) {
				foreach ( $lists['lists'] as $list ) {
					$this->lists_process->push_to_queue( $list );
				}
				$this->lists_process->save()->dispatch();
			}
		} else {
			$lists = $this->get_local_lists();
		}

		return $lists;
	}

	/**
	 * Function to get the list from MailChimp API.
	 *
	 * @since 0.1.0
	 *
	 * @param string $api_key The MailChimp API key.
	 * @return mixed Returns an object of the lists, or an Exception if the API key added
	 *               is invalid.
	 */
	private function get_remote_lists( $api_key ) {

		$mailchimp = new MailChimp( $api_key );
		$response  = $mailchimp->get( 'lists', [
			'count'  => $this->get_lists_per_page(),
			'fields' => 'lists.name,lists.id,lists.stats,lists.double_optin,total_items',
		] );

		if ( array_key_exists( 'lists', $response ) ) {
			update_option( 'wp_chimp_lists_total_items', absint( $response['total_items'] ), false );
		}

		return [
			'lists'       => Utilities\sort_mailchimp_lists( $response['lists'] ),
			'total_items' => absint( $response['total_items'] ),
		];
	}

	/**
	 * Function to get the MailChimp list from the cache or the database.
	 *
	 * @since 0.1.0
	 *
	 * @return array Returns an object of the lists, empty array, or an Exception
	 *               if the API key added is invalid.
	 */
	private function get_local_lists() {

		return [
			'lists'       => $this->lists_query->query(),
			'total_items' => $this->get_lists_total_items(),
		];
	}

	/**
	 * Function get the MailChimp API key set.
	 *
	 * @since 0.1.0
	 *
	 * @return string The MailChimp API key or an empty string if it has not
	 *                yet been set.
	 */
	private function get_the_mailchimp_api_key() {
		return get_option( 'wp_chimp_api_key', '' );
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
	private function is_lists_init() {
		return (bool) get_option( 'wp_chimp_lists_init', 0 );
	}

	/**
	 * Function to get the number of lists as obtained from the
	 * MailChimp API response.
	 *
	 * @since 0.1.0
	 *
	 * @return int The total items of the lists.
	 */
	private function get_lists_total_items() {

		$total_items = get_option( 'wp_chimp_lists_total_items', 0 );
		return absint( $total_items );
	}

	/**
	 * Function to get the list item to show per page.
	 *
	 * @since 0.1.0
	 *
	 * @return integer The nubmer of lists per page.
	 */
	private function get_lists_per_page() {
		return 10;
	}
}
