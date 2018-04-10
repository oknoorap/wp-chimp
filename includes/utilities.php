<?php
/**
 * Functions, Utilities, and Helpers
 *
 * @since   0.1.0
 * @package WP_Chimp\includes
 */

namespace WP_Chimp\Utilities;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! function_exists( __NAMESPACE__ . 'sort_mailchimp_data' ) ) :

	/**
	 * Function to sort out MailChimp API.
	 *
	 * The function will select select few data out of the
	 * MailChimp API response.
	 *
	 * @since  0.1.0
	 *
	 * @param  array $raw_data The MailChimp API response.
	 * @return array
	 */
	function sort_mailchimp_data( $raw_data ) {

		$sorted_data = [];
		foreach ( $raw_data as $key => $list ) {
			$sorted_data[ $key ] = [
				'list_id'      => sanitize_key( $list['id'] ),
				'name'         => sanitize_text_field( $list['name'] ),
				'subscribers'  => absint( $list['stats']['member_count'] ),
				'double_optin' => true === $list['double_optin'] ? 1 : 0,
			];
		}

		return $sorted_data;
	}

endif; // ! function_exists( 'wp_chimp_sort_mailchimp_data' ).
