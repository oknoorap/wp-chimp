<?php
/**
 * The file that defines the class and the methods to query
 * *_chimp_mailchimp_lists table.
 *
 * @link       https://wp-chimp.com
 * @since      0.1.0
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/includes
 */

namespace WP_Chimp\Storage;

use \WP_Error;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * The class to query the *_chimp_mailchimp_lists table
 *
 * @since 0.1.0
 */
final class MailChimp_Lists_Query {

	/**
	 * The columns and its value.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var    array
	 */
	protected $default_data;

	/**
	 * The class constructor.
	 *
	 * @since  0.1.0
	 * @access public
	 */
	public function __construct() {

		$this->default_data = [
			'list_id'      => '',
			'name'         => '',
			'subscribers'  => 0,
			'double_optin' => 0,
			'synced_at'    => '0000-00-00 00:00:00',
		];
	}

	/**
	 * Function to get all MailChimp list from the table.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @return array An associative array of the MailChimp list ID.
	 *               Or, an empty array if the table is empty.
	 */
	public function query() {
		global $wpdb;

		$cache_key = 'lists';

		/**
		 * First, we will check whether the data is available in
		 * the Object Cache.
		 *
		 * @var false|array Returns false if the cache is not available,
		 *                  and should return an array if the data
		 *                  exists.
		 */
		$lists = wp_cache_get( $cache_key, 'wp_chimp_lists' );

		if ( false === $lists ) {

			$lists = $wpdb->get_results("
				SELECT list_id, name, subscribers, double_optin
				FROM $wpdb->chimp_mailchimp_lists
			", ARRAY_A );

			wp_cache_add( $cache_key, $lists, 'wp_chimp_lists' );
		}

		return $lists;
	}

	/**
	 * Function to return only the IDs of the list.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @return array An associative array of the MailChimp list ID.
	 *               Or, an empty array if the table is empty.
	 */
	public function get_the_ids() {
		global $wpdb;

		$cache_key = 'list_ids';
		$list_ids  = wp_cache_get( $cache_key, 'wp_chimp_lists' );

		if ( false === (bool) $list_ids ) {

			$results = $wpdb->get_results("
				SELECT list_id
				FROM $wpdb->chimp_mailchimp_lists
			", ARRAY_A );

			$list_ids = [];
			foreach ( $results as $result ) {
				$list_ids[] = $result['list_id'];
			}

			wp_cache_add( $cache_key, $list_ids, 'wp_chimp_lists' );
		}

		return $list_ids;
	}

	/**
	 * Function to get the MailChimp list by the List ID.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  string $id The MailChimp list ID {@link https://kb.mailchimp.com/lists/manage-contacts/find-your-list-id}.
	 * @return array An associative array of the List from the database.
	 *               Or, an empty array if the list is not present,
	 *               with the $id is not present.
	 */
	public function get_by_the_id( $id = '' ) {
		global $wpdb;

		if ( is_string( $id ) && ! empty( $id ) ) {

			$list = $wpdb->get_row( $wpdb->prepare("
				SELECT list_id, name, subscribers, double_optin
				FROM $wpdb->chimp_mailchimp_lists
				WHERE list_id = %s
			", [ $id ] ), ARRAY_A );
		}

		return null === $list ? [] : $list;
	}

	/**
	 * Function to insert a new entry of MailChimp List to the table.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  array $data The data to add into the table.
	 * @return int|bool|WP_Error Should return 1 the data has been success fully added.
	 *                           Otherwise, it should return false if an error
	 *                           occured, or WP_Error if it is failed to
	 *                           insert the data.
	 */
	public function insert( array $data ) {
		global $wpdb;

		$data = $this->sanitize_columns( $data );
		$data = wp_parse_args( $data, $this->default_data );

		if ( $this->is_columns_data_invalid( $data ) ) {
			return false;
		}

		/**
		 * First let's check the 'list_id' existance. We'll need to be sure that
		 * the ID is a string, it is not an empty, and the row with the ID
		 * does not exist.
		 */
		$current_id = $this->get_by_the_id( $data['list_id'] );

		if ( ! empty( $current_id ) ) {
			return new WP_Error( 'wp_signups_domain_exists', esc_html__( 'That signup already exists.', 'wp-chimp' ), $this );
		}

		$inserted = $wpdb->insert( $wpdb->chimp_mailchimp_lists, self::sanitize_values( $data ),
			[ '%s', '%s', '%d', '%d', '%s' ]
		);

		if ( false === $inserted ) { // If the data is successfully inserted, add to the cache.
			// Translators: %s is the MailChimp list ID.
			return new WP_Error( 'wp_signups_insert_list_error', sprintf( esc_html__( 'Inserting the MailChimp list ID %s failed.', 'wp-chimp' ), $data['list_id'] ), $data );
		} else {
			$cache_key = "list:{$data['list_id']}";
			wp_cache_add( $cache_key, self::sanitize_values( $data ), 'wp_chimp_lists' );

			// Clean cache containing all the lists.
			self::clean_cache( 'lists' );
		}

		return $inserted;
	}

	/**
	 * Function to update the existing entry in the MailChimp List table.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  string $id   The MailChimp list ID {@link https://kb.mailchimp.com/lists/manage-contacts/find-your-list-id}
	 *                      to be updated.
	 * @param  array  $data An array of data to be updated to the $id.
	 * @return int|false Number of rows affected/selected or false on error
	 */
	public function update( $id = '', array $data ) {
		global $wpdb;

		if ( $this->is_columns_data_invalid( $data ) ) {
			return false;
		}

		unset( $data['list_id'] ); // Remove the `list_id` from the updated column.

		$updated = $wpdb->update( $wpdb->chimp_mailchimp_lists, self::sanitize_values( $data ),
			[ 'list_id' => $id ],
			[ '%s', '%d', '%d', '%s' ],
			[ '%s' ]
		);

		if ( false !== $updated ) { // If the data is updated, update the cache as well.
			$cache_key = "list:{$id}";
			wp_cache_set( $cache_key, self::sanitize_values( $data ), 'wp_chimp_lists' );

			// Clean cache containing all the lists.
			self::clean_cache( 'lists' );
		}

		return $updated;
	}

	/**
	 * Function to delete a MailChimp list by the ID.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  string $id The MailChimp list ID {@link https://kb.mailchimp.com/lists/manage-contacts/find-your-list-id}.
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function delete( $id = '' ) {
		global $wpdb;

		$deleted = $wpdb->delete( $wpdb->chimp_mailchimp_lists,
			[ 'list_id' => $id ],
			[ '%s' ]
		);

		if ( false !== $deleted ) {
			self::clean_cache( 'lists' );
			self::clean_cache( "list{$id}" );
		}

		return $deleted;
	}

	/**
	 * Function to empty the records in the `*_chimp_mailchimp_lists` table
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @return int|false Number of rows affected/selected or false on error
	 */
	public function truncate() {
		global $wpdb;

		$emptied = $wpdb->query( "TRUNCATE TABLE $wpdb->chimp_mailchimp_lists" );

		if ( true === $emptied ) {

			self::clean_cache( 'lists' );

			$mailchimp_ids = $this->get_the_ids();
			foreach ( $mailchimp_ids as $id ) {
				self::clean_cache( "list{$id}" );
			}
		}

		/**
		 * ...For CREATE, ALTER, TRUNCATE and DROP SQL statements, (which affect
		 * whole tables instead of specific rows) this function returns
		 * TRUE on success...
		 *
		 * {@link https://codex.wordpress.org/Class_Reference/wpdb#Running_General_Queries}
		 */
		return $emptied;
	}

	/**
	 * Function to check if the required column contains valid data
	 *
	 * @since  0.1.0
	 * @access private
	 *
	 * @param  array $data The data containing the columns to insert to the database.
	 * @return boolean
	 */
	private function is_columns_data_invalid( array $data ) {

		/**
		 * First let's check the list_id existance. We'll need to be sure that
		 * the ID is a string, it is not an empty, and the row with the ID
		 * does not exist.
		 */
		if ( ! is_string( $data['list_id'] ) || empty( $data['list_id'] ) ) {
			return true;
		}

		/**
		 * Do not insert the entry to the database if the MailChimp name is empty,
		 * or, if it is not the expected data type.
		 */
		if ( ! is_string( $data['name'] ) || empty( $data['name'] ) ) {
			return true;
		}
	}

	/**
	 * Function to filter-out array that should not to include be in the table
	 *
	 * @since  0.1.0
	 * @access private
	 *
	 * @param  array $data List of columns and the values to add to the table.
	 * @return array       List of columns with the invalid columnes filtered-out
	 */
	private function sanitize_columns( array $data ) {

		$diffs = array_diff_key( $data, $this->default_data );
		foreach ( $diffs as $key => $diff ) {
			unset( $data[ $key ] );
		}

		return $data;
	}

	/**
	 * Function to sanitize values before inserting to the table
	 *
	 * @since  0.1.0 Strip all the tags.
	 *
	 * @param  array $data The unsanitize data.
	 * @return array Sanitized values.
	 */
	static private function sanitize_values( array $data ) {

		$sanitized_data = [];
		foreach ( $data as $key => $value ) {
			$sanitized_data[ $key ] = wp_strip_all_tags( $value );
		}

		return $sanitized_data;
	}

	/**
	 * Function to clean the list from the Object Cache
	 *
	 * @since  0.1.0
	 *
	 * @param  string $cache_key The list ID to delete from the cache.
	 * @return void
	 */
	static private function clean_cache( $cache_key ) {
		global $_wp_susp_wp_suspend_cache_invalidation;

		if ( ! empty( $_wp_suspend_cache_invalidation ) ) { // Bail if cache invalidation is suspended.
			return;
		}

		wp_cache_delete( $cache_key, 'wp_chimp_lists' ); // Delete list from the cache.
	}
}
