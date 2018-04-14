<?php
/**
 * Functions, Utilities, and Helpers
 *
 * @since   0.1.0
 * @package WP_Chimp\includes
 */

namespace WP_Chimp\Includes\Utilities;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly, abort.
	die;
}

if ( ! function_exists( __NAMESPACE__ . '\\sort_mailchimp_lists' ) ) :

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
	function sort_mailchimp_lists( $raw_data ) {

		$sorted_data = [];
		foreach ( $raw_data as $key => $list ) {

			$list_id          = isset( $list['id'] ) ? $list['id'] : '';
			$list_name        = isset( $list['name'] ) ? $list['name'] : '';
			$list_subscribers = isset( $list['stats'] ) && isset( $list['member_count'] ) ? $list['stats'] : [];
			$list_optin       = isset( $list['double_optin'] ) ? $list['double_optin'] : 0;

			if ( ! empty( $list_id ) ) {
				$sorted_data[ $key ] = [
					'list_id'      => sanitize_key( $list['id'] ),
					'name'         => sanitize_text_field( $list['name'] ),
					'subscribers'  => absint( $list['stats']['member_count'] ),
					'double_optin' => true === $list['double_optin'] ? 1 : 0,
				];
			}
		}

		return $sorted_data;
	}

endif; // wp_chimp_sort_mailchimp_data.

if ( ! function_exists( __NAMESPACE__ . '\\convert_keys_to_camelcase' ) ) :

	/**
	 * Function to transform the array keys to camelCase.
	 *
	 * This function will be used to convert associative array that
	 * will be used in JavaScript.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $inputs Associative array.
	 * @return array Associative array with the key converted to camelcase
	 */
	function convert_keys_to_camelcase( array $inputs ) {

		$inputs_converted = [];
		foreach ( $inputs as $key => $input ) {
			$key = lcfirst( implode( '', array_map( 'ucfirst', explode( '_', $key ) ) ) );
			if ( is_array( $input ) ) {
				$input = convert_keys_to_camelcase( $input );
			}
			$inputs_converted[ $key ] = $input;
		}

		return $inputs_converted;
	}

endif; // convert_keys_to_camelcase
