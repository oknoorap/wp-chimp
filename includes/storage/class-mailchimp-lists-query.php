<?php

namespace WP_Chimp\Storage;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class that register new menu in the Admin area and load the page.
 *
 * @since 0.1.0
 */
final class MailChimp_Lists_Query {

	/**
	 * Function to get all MailChimp list from the table.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @return array An associative array of the MailChimp list ID.
	 *               Or, an empty array if the table is empty.
	 */
	static public function query() {

		global $wpdb;

		$cache_key = 'get_lists';

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
				SELECT list_id, name, subscribers, double_opt_in
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
	static public function get_the_ids() {

		global $wpdb;

		$cache_key = 'get_ids';
		$list_ids  = wp_cache_get( $cache_key, 'wp_chimp_lists' );

		if ( false === $list_ids ) {
			$results = $wpdb->get_results("
				SELECT list_id
				FROM $wpdb->chimp_mailchimp_lists
			", ARRAY_A );

			foreach ( $results as $value ) {
				$list_ids[] = $value['list_id'];
			}
			wp_cache_add( $cache_key, $list_ids, 'wp_chimp_lists' );
		}

		return false === $list_ids ? [] : $list_ids;
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
	static public function get_by_the_id( $id = '' ) {

		global $wpdb;

		$cache_key = "get_list_id:{$id}";
		$list      = wp_cache_get( $cache_key, 'wp_chimp_lists' );

		if ( false === $list && is_string( $id ) && ! empty( $id ) ) {

			$list = $wpdb->get_row( $wpdb->prepare("
				SELECT list_id, name, subscribers, double_opt_in
				FROM $wpdb->chimp_mailchimp_lists
				WHERE list_id = %s
			", [ $id ] ), ARRAY_A );

			wp_cache_add( $cache_key, $list, 'wp_chimp_lists' );
		}

		return false === $list ? [] : $list;
	}

	/**
	 * Function to insert a new entry of MailChimp List to the table.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  array $data The data to add into the table.
	 * @return bool Returns true if the data has been success fully added.
	 *              Otherwise, it returns false if an error occured.
	 */
	static public function insert( array $data ) {

		global $wpdb;

		$defaults = [
			'list_id'       => '',
			'name'          => '',
			'subscribers'   => 0,
			'double_opt_in' => 0,
			'synced_at'     => '0000-00-00 00:00:00',
		];

		// Filter-out array that should not to include to the database.
		$diffs = array_diff_key( $data, $defaults );
		foreach ( $diffs as $key => $diff ) {
			unset( $data[ $key ] );
		}

		$data       = wp_parse_args( $data, $defaults );
		$current_id = self::get_by_the_id( $data['list_id'] );

		/**
		 * First let's check the list_id existance. We'll need to be sure that
		 * the ID is a string, it is not an empty, and the row with the ID
		 * does not exist.
		 */
		if ( ! is_string( $data['list_id'] ) || empty( $data['list_id'] ) || ! empty( $current_id ) ) {
			return false;
		}

		/**
		 * Do not insert the entry to the database if the MailChimp name is empty,
		 * or, if it is not the expected data type.
		 */
		if ( ! is_string( $data['name'] ) || empty( $data['name'] ) ) {
			return false;
		}

		return $wpdb->insert( $wpdb->chimp_mailchimp_lists, $data, [ '%s', '%s', '%d', '%d', '%s' ] );
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

		if ( ! is_string( $id ) || empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->chimp_mailchimp_lists, $data,
			[ 'list_id' => $id ],
			[ '%s', '%d', '%d', '%s' ],
			[ '%s' ]
		);
	}

	/**
	 * Function to delete a MailChimp list by the ID.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  string $id The MailChimp list ID {@link https://kb.mailchimp.com/lists/manage-contacts/find-your-list-id}.
	 */
	public function delete( $id = '' ) {
		return true;
	}
}
