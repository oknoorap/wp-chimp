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

use WP_Chimp\Includes;
use WP_Chimp\Includes\Lists;
use WP_Chimp\Includes\Utilities;

use DrewM\MailChimp\MailChimp;

/**
 * The class that register the custom '/list' endpoint to WP-API.
 *
 * @since  0.1.0
 * @author Thoriq Firdaus <thoriqoe@gmail.com>
 */
class REST_Lists_Controller extends WP_REST_Controller {

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
	 * @var    DrewM\MailChimp\MailChimp
	 */
	protected $mailchimp;

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
	 * Undocumented variable
	 *
	 * @var array
	 */
	public $exclude_columns;

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
				'args' => $this->get_collection_params(),
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
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
			],
			[
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'update_item_permissions_check' ],
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		]);
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
				'description' => __( 'Current page of the collection.', 'wp-chimp' ),
				'type' => 'integer',
				'sanitize_callback' => 'absint',
				'default' => self::get_default_page(),
			],
			'per_page' => [
				'description' => __( 'Maximum number of items to be returned in result set.', 'wp-chimp' ),
				'type' => 'integer',
				'sanitize_callback' => 'absint',
				'default' => self::get_lists_total_items(),
			],
		];
	}

	/**
	 * Retrieves the Lists schema, conforming to JSON Schema.
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
				'list_id' => [
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
				'double_optin' => [
					'description' => __( 'Whether or not to require the subscriber to confirm subscription.', 'wp-chimp' ),
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
	 * Check if a given request has access to get a specific item.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool
	 */
	public function get_items_permissions_check( $request ) {

		$wp_nonce = $request->get_header( 'X-WP-Nonce' );
		$wp_chimp_nonce = $request->get_header( 'X-WP-Chimp-Nonce' );

		if ( wp_verify_nonce( $wp_chimp_nonce, 'wp-chimp-setting' ) ) { // wpChimpSettingState.nonce.
			return true;
		}

		if ( wp_verify_nonce( $wp_nonce, 'wp_rest' ) ) { // wpAPISetting.nonce.

			/**
			 * Limit the column to show to the user. If the user is not able to view the 'Setting' page,
			 * possibly they should not view the number of 'subscribers' and the 'double_optin'
			 * status.
			 */
			$this->exclude_columns = [ 'double_optin', 'subscribers' ];

			return true;
		}

		return new WP_Error( 'rest_not_allowed', __( 'Sorry, you\'re not allowed to view this.' ), [ 'status' => rest_authorization_required_code() ] );
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
	 * Checks if a request has access to update the specified term.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return bool
	 */
	public function update_item_permissions_check( $request ) {
		return true;
	}

	/**
	 * Function to return the response from '/lists' endpoints.
	 *
	 * @since  0.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function get_items( $request ) {

		$lists = [];
		$items = [];

		$page_num = absint( $request->get_param( 'page' ) );
		$per_page = absint( $request->get_param( 'per_page' ) );

		$lists = $this->get_lists( [
			'page' => $page_num,
			'per_page' => $per_page,
			'offset' => self::get_lists_offset( $page_num, $per_page ),
		] );

		$total_items = self::get_lists_total_items();
		$total_pages = self::get_lists_total_pages( $per_page );

		foreach ( $lists as $key => $list ) {
			$data = $this->prepare_item_for_response( $list, $request );
			$items[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $items );

		if ( $page_num ) {
			$response->header( 'X-WP-Chimp-Lists-Page', $page_num );
		}

		if ( $total_items ) {
			$response->header( 'X-WP-Chimp-Lists-Total', $total_items );
		}

		if ( $total_pages ) {
			$response->header( 'X-WP-Chimp-Lists-TotalPages', $total_pages );
		}

		return $response;
	}

	/**
	 * Function to return the response from '/list' API endpoint.
	 *
	 * @since  0.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function get_item( $request ) {

		$response = [];

		$list_id = $request->get_param( 'id' );
		$list = $this->lists_query->get_by_the_id( $list_id );

		if ( ! empty( $list ) ) {
			$item = $this->prepare_item_for_response( $list, $request );
			$response = rest_ensure_response( $item );
		}

		return $response;
	}

	/**
	 * Subscribe an email to a MailChimp list.
	 *
	 * @since  0.1.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Response object.
	 */
	public function update_item( $request ) {

		$response = [];

		$list_id  = $request->get_param( 'id' );
		$email = $request->get_param( 'email' );

		if ( ! is_email( $email ) ) {

			$response = rest_ensure_response([
				'email_address' => $email,
				'status' => 'invalid_email',
			]);

			return $response;
		}

		if ( ! empty( $list_id ) && $this->mailchimp instanceof MailChimp ) {

			$status = $this->get_subscription_status();
			$subscription = $this->mailchimp->post( "lists/{$list_id}/members", [
				'email_address' => $email,
				'status' => $status,
			]);

			$response = rest_ensure_response( $subscription );

			return $response;
		}

		return rest_ensure_response( $response );
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
		$item = $this->filter_item( $item );

		$props = $schema['properties'];

		if ( ! empty( $props['list_id'] ) && isset( $item['list_id'] ) ) {
			$data['list_id'] = wp_strip_all_tags( $item['list_id'], true );
		}

		if ( ! empty( $props['name'] ) && isset( $item['name'] ) ) {
			$data['name'] = wp_strip_all_tags( $item['name'], true );
		}

		if ( ! empty( $props['subscribers'] ) && isset( $item['subscribers'] ) ) {
			$data['subscribers'] = absint( $item['subscribers'] );
		}

		if ( ! empty( $props['double_optin'] ) && isset( $item['double_optin'] ) ) {
			$data['double_optin'] = absint( $item['double_optin'] );
		}

		return rest_ensure_response( $data ); // Wrap the data in a response object.
	}

	/**
	 * Filter the Lists column output.
	 *
	 * @since 0.1.0
	 *
	 * @param array $item The list item.
	 * @return array The list item with some data sorted out.
	 */
	protected function filter_item( array $item ) {

		$excludes = $this->get_exclude_columns();

		if ( ! is_array( $excludes ) || empty( $excludes ) ) {
			return $item;
		}

		foreach ( $item as $key => $value ) {
			if ( in_array( $key, $excludes, true ) ) {
				unset( $item[ $key ] );
			}
		}

		return $item;
	}

	protected function get_exclude_columns() {
		return (array) $this->exclude_columns;
	}

	/**
	 * Retieve MailChimp Lists.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args The arguments passed in the Endpoint query strings.
	 * @return mixed Return an object if it is successfully retrieved the MailChimp list,
	 *               or an empty array if not. It may also return an Exception if
	 *               the key, added is invalid.
	 */
	protected function get_lists( array $args ) {
		return $this->lists_query->query( $args );
	}

	/**
	 * Retrieve the subscription status for the subscriber.
	 *
	 * @since 0.1.0
	 * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
	 *
	 * @param string $list_id A MailChimp List ID.
	 * @return string Returns `pending` if the Double Optin option is enabled on the list,
	 *                otherwise returns `subscribed`.
	 */
	protected function get_subscription_status( $list_id ) {

		$list = $this->lists_query->get_by_the_id( $list_id );
		return isset( $list['double_optin'] ) && 1 === absint( $list['double_optin'] ) ? 'pending' : 'subscribed';
	}

	/**
	 * Retrieve the Lists offset.
	 *
	 * Offset the result set by a specific number of items. Primarily used for determinating
	 * pagination on the Lists table in the Settings page.
	 *
	 * @since 0.1.0
	 *
	 * @param int $page_num The page number requested.
	 * @param int $per_page The page number requested.
	 * @return int The offset number of the given page requested.
	 */
	protected static function get_lists_offset( $page_num, $per_page ) {

		$offset = ( absint( $page_num ) - 1 ) * absint( $per_page );
		return absint( $offset );
	}

	/**
	 * Retrieve the total pages.
	 *
	 * The total number could be used for determinating the pagination of the Lists table
	 * in the Settings page.
	 *
	 * @since 0.1.0
	 *
	 * @param int $per_page Maximum number of items to be returned in result.
	 * @return int The number of pages.
	 */
	protected static function get_lists_total_pages( $per_page ) {

		$total_items = self::get_lists_total_items();
		$total_pages = 0 >= $total_items ? 1 : ceil( $total_items / absint( $per_page ) );

		return absint( $total_pages );
	}

	/**
	 * Retrieve the default page number.
	 *
	 * The number could be overidden by adding the 'page' parameter on the endpoint URL.
	 *
	 * @since 0.1.0
	 *
	 * @return int The default page number.
	 */
	protected static function get_default_page() {
		return 1;
	}

	/**
	 * Retrieve the number of items.
	 *
	 * The number is obtained from the MailChimp API response during the initialization,
	 * when the API key is first added.
	 *
	 * @since 0.1.0
	 * @see Admin\Page->updated_option();
	 *
	 * @return int The total items of the lists.
	 */
	protected static function get_lists_total_items() {

		$total_items = Includes\get_the_lists_total_items();
		return absint( $total_items );
	}
}
