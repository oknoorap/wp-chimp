<?php
/**
 * The file that defines a REST controller for '/lists' endpoints.
 *
 * @link https://wp-chimp.com
 * @since 0.1.0
 * @package WP_Chimp/Includes
 */

namespace WP_Chimp\Includes\Endpoints;

/* If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
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
	 * The plugin API version.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	const VERSION = 'v1';

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
	protected $lists_query;

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
		$this->version = $version;

		$this->namespace = self::get_namespace();
		$this->rest_base = self::get_rest_base();
	}

	/**
	 * Function ot get the plugin api namespace.
	 *
	 * @since 0.1.0
	 *
	 * @return string The name space and the version.
	 */
	public static function get_namespace() {
		return 'wp-chimp/' . self::VERSION;
	}

	/**
	 * Get REST Base.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_rest_base() {
		return 'lists';
	}

	/**
	 * Function to register the MailChimp instance.
	 *
	 * @since 0.1.0
	 *
	 * @param MailChimp $mailchimp The MailChimp instance.
	 * @return void
	 */
	public function set_mailchimp( MailChimp $mailchimp ) {
		$this->mailchimp = $mailchimp;
	}

	/**
	 * Function to register the Query instance
	 *
	 * @since 0.1.0
	 *
	 * @param Lists\Query $query The List\Query instance to retrieve the lists from the database.
	 * @return void
	 */
	public function set_lists_query( Lists\Query $query ) {
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
	public function set_lists_process( Lists\Process $process ) {
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
		 * Register the '/lists' route to retrieve a collection of MailChimp list.
		 *
		 * @uses WP_REST_Server
		 */
		register_rest_route( $this->namespace, $this->rest_base, [
			[
				'methods' => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args' => $this->get_collection_params(),
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		]);

		/**
		 * Register the '/list' route to retrieve a single MailChimp list with their ID.
		 *
		 * @uses WP_REST_Server
		 */
		register_rest_route( $this->namespace, $this->rest_base . '/(?P<id>[\w-]+)', [
			[
				'methods' => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
				'args' => [
					'context' => $this->get_context_param( [ 'default' => 'view' ] ),
				],
			],
			[
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'get_item_permissions_check' ],
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

		$method = $request->get_method();

		if ( 'GET' === $method ) {
			return current_user_can( 'manage_options' );
		} else {
			return true;
		}
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
	 * Function to return the response from '/lists' endpoints.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function get_items( $request ) {

		$lists = [];

		$total_items = null;
		$total_pages = null;

		$context = $request->get_param( 'context' );
		$page = isset( $request['page'] ) && 0 < $request['page'] ? $request['page'] : 1;

		if ( 'block' === $context ) {

			$total_items = $this->get_lists_total_items();
			$local_lists = $this->get_local_lists([
				'count' => $total_items,
			]);

			foreach ( $local_lists as $key => $value ) {

				unset( $value['subscribers'] );
				unset( $value['double_optin'] );

				$lists[ $key ] = $value;
			}
		} else {

			$lists = $this->get_lists( [
				'offset' => self::get_lists_offset( $page ),
			] );

			$total_items = self::get_lists_total_items();
			$total_pages = ceil( $total_items / self::get_lists_per_page() );
		}

		$items = [];
		foreach ( $lists as $key => $list ) {
			$data = $this->prepare_item_for_response( $list, $request );
			$items[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $items );

		if ( $page ) {
			$response->header( 'X-WP-Chimp-Lists-Page', absint( $page ) );
		}

		if ( $total_items ) {
			$response->header( 'X-WP-Chimp-Lists-Total', absint( $total_items ) );
		}

		if ( $total_pages ) {
			$response->header( 'X-WP-Chimp-Lists-TotalPages', absint( $total_pages ) );
		}

		return $response;
	}

	/**
	 * Function to return the response from '/list' API endpoint.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function get_item( $request ) {

		$response = [];
		$list_id = $request->get_param( 'id' );
		$data = $this->get_local_list_by_the_id( $list_id );

		if ( ! empty( $data ) ) {
			$item     = $this->prepare_item_for_response( $data, $request );
			$response = rest_ensure_response( $item );
		}

		return $response;
	}

	/**
	 * Subscribe an email to a MailChimp list.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function update_item( $request ) {

		$response = [];
		$list_id = $request->get_param( 'id' );
		$email = $request->get_param( 'email' );

		if ( ! is_email( $email ) ) {
			return rest_ensure_response([
				'email_address' => $email,
				'status' => 'invalid_email',
			]);
		}

		if ( ! empty( $list_id ) ) {

			$status = $this->lists_query->is_double_optin() ? 'pending' : 'subscribed';
			$response = $this->mailchimp->post( "lists/{$list_id}/members", [
				'email_address' => $email,
				'status' => $status, // Valid values: subscribed, unsubscribed, cleaned, pending.
			]);

			return rest_ensure_response( $response );
		}
	}

	/**
	 * Prepares a single MailChimp list output for response.
	 *
	 * @since 0.1.0
	 *
	 * @param array           $item The detail of a MailChimp list (e.g. list_id, name, etc.).
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function prepare_item_for_response( $item, $request ) {

		$data = [];
		$schema = $this->get_item_schema();
		$props = $schema['properties'];

		if ( ! empty( $props['listId'] ) && isset( $item['list_id'] ) ) {
			$data['listId'] = wp_strip_all_tags( $item['list_id'], true );
		}

		if ( ! empty( $props['name'] ) && isset( $item['name'] ) ) {
			$data['name'] = wp_strip_all_tags( $item['name'], true );
		}

		if ( ! empty( $props['subscribers'] ) && isset( $item['subscribers'] ) ) {
			$data['subscribers'] = absint( $item['subscribers'] );
		}

		if ( ! empty( $props['doubleOptin'] ) && isset( $item['double_optin'] ) ) {
			$data['doubleOptin'] = absint( $item['double_optin'] );
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
			'page'    => [
				'description' => __( 'Current page of the collection.', 'wp-chimp' ),
				'type' => 'integer',
				'sanitize_callback' => 'absint',
			],
			'context' => $this->get_context_param( [
				'default' => 'view',
				'enum' => [ 'view', 'block' ],
			] ),
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
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'title' => __( 'MailChimp Lists', 'wp-chimp' ),
			'type' => 'object',
			'properties' => [
				'listId' => [
					'description' => __( 'A string that uniquely identifies this list.', 'wp-chimp' ),
					'type' => 'string',
					'readonly' => true,
				],
				'name' => [
					'description' => __( 'The name of the list.', 'wp-chimp' ),
					'type' => 'string',
					'readonly' => true,
					'arg_options' => 'sanitize_text_field',
				],
				'subscribers' => [
					'description' => __( 'The number of active members in the list.', 'wp-chimp' ),
					'type' => 'integer',
					'readonly' => true,
					'arg_options' => [
						'sanitize_callback' => 'absint',
					],
				],
				'doubleOptin' => [
					'description' => __( 'Whether or not to require the subscriber to confirm subscription via email.', 'wp-chimp' ),
					'type' => 'boolean',
					'readonly' => true,
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
	 * @param array $args The arguments passed in the Endpoint query strings.
	 * @return mixed Return an object if it is successfully retrieved the MailChimp list,
	 *               or an empty array if not. It may also return an Exception if
	 *               the key, added is invalid.
	 */
	private function get_lists( array $args ) {

		$lists = [];
		$args = wp_parse_args( $args, [
			'count' => $this->get_lists_per_page(),
		]);

		if ( 1 !== self::is_lists_init() ) {
			$lists = $this->get_remote_lists( $args );
		} else {
			$lists = $this->get_local_lists( $args );
		}

		return $lists;
	}

	/**
	 * Function to get the list from MailChimp API.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args The arguments passed in the Endpoint query strings.
	 * @return mixed Returns an object of the lists, or an Exception if the API key added
	 *               is invalid.
	 */
	private function get_remote_lists( array $args = [] ) {

		$remote_lists = [];
		$api_args = [
			'fields' => 'lists.name,lists.id,lists.stats,lists.double_optin',
			'count' => self::get_lists_total_items(),
		];

		$lists = $this->mailchimp->get( 'lists', $api_args );

		if ( $this->mailchimp->success() ) {
			$remote_lists = Utilities\sort_mailchimp_lists( $lists['lists'] );
		}

		/**
		 * Make sure to only kick-in the "background processing" when it hasn't
		 * been instantiated yet. While it is in progress, it should not
		 * dispatch another new processing.
		 */
		if ( 0 !== count( $remote_lists ) && 0 === self::is_lists_init() ) {
			foreach ( $remote_lists as $list ) {
				$this->lists_process->push_to_queue( $list );
			}
			$this->lists_process->save()->dispatch();
		}

		return self::remote_lists_response( $remote_lists, $args );
	}

	/**
	 * Function to get the MailChimp list from the cache or the database.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args The arguments passed in the Endpoint query strings.
	 * @return array Returns an object of the lists, empty array, or an Exception
	 *               if the API key added is invalid.
	 */
	private function get_local_lists( $args ) {
		return $this->lists_query->query( $args );
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $list_id
	 * @return array
	 */
	private function get_local_list_by_the_id( $list_id ) {
		return $this->lists_query->get_by_the_id( $list_id );
	}

	/**
	 * Function get the MailChimp API key set.
	 *
	 * @since 0.1.0
	 *
	 * @return string The MailChimp API key or an empty string if it has not
	 *                yet been set.
	 */
	private static function get_mailchimp_api_key() {
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
	private static function is_lists_init() {
		$init = get_option( 'wp_chimp_lists_init', 0 );
		return absint( $init );
	}

	/**
	 * Function to get the number of lists as obtained from the
	 * MailChimp API response.
	 *
	 * @since 0.1.0
	 *
	 * @return int The total items of the lists.
	 */
	private static function get_lists_total_items() {
		$total_items = get_option( 'wp_chimp_lists_total_items', 0 );
		return absint( $total_items );
	}

	/**
	 * Function to get the list item to show per page.
	 *
	 * @since 0.1.0
	 *
	 * @return int The nubmer of lists per page.
	 */
	private static function get_lists_per_page() {
		return 10;
	}

	/**
	 * Function to get the offset lists.
	 *
	 * @since 0.1.0
	 *
	 * @param int $page The page number requested.
	 * @return int The offset number of the given page requested.
	 */
	private static function get_lists_offset( $page ) {
		$offset = ( $page - 1 ) * self::get_lists_per_page();
		return absint( $offset );
	}

	/**
	 * Function to filter the lists output for WP-API response.
	 *
	 * Ensure that the output follows the parameter passsed in the endpoint
	 * query strings.
	 *
	 * @since 0.1.0
	 *
	 * @param array $lists The remote lists retrieved from MailChimp API.
	 * @param array $args The arguments passed in the endpoint query strings.
	 * @return array The filtered MailChimp lists.
	 */
	protected static function remote_lists_response( array $lists, array $args ) {

		$offset = isset( $args['offset'] ) ? absint( $args['offset'] ) : 0;
		$count  = self::get_lists_per_page();

		return array_slice( $lists, $offset, $count );
	}
}
