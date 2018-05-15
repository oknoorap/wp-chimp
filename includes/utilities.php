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

if ( ! function_exists( __NAMESPACE__ . '\\from_snake_to_camel' ) ) :

	/**
	 * Convert string from snake_case to camelCase
	 *
	 * @since 0.1.0
	 *
	 * @param string $string The string to convert in camelCase format.
	 * @return string The converted string in snake_case
	 */
	function from_snake_to_camel( $string ) {
		return lcfirst( implode( '', array_map( 'ucfirst', explode( '_', $string ) ) ) );
	}

endif;

if ( ! function_exists( __NAMESPACE__ . '\\defrom_snake_to_camel' ) ) :

	/**
	 * Convert string from camelCase to snake_case
	 *
	 * @since 0.1.0
	 *
	 * @param string $string The string to convert in camelCase format.
	 * @return string The converted string in snake_case
	 */
	function from_camel_to_snake( $string ) {
		return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
	}

endif;

if ( ! function_exists( __NAMESPACE__ . '\\convert_keys_to_camel_case' ) ) :

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
	function convert_keys_to_camel_case( array $inputs ) {

		$inputs_converted = [];
		foreach ( $inputs as $key => $input ) {
			$key = from_snake_to_camel( $key );
			if ( is_array( $input ) ) {
				$input = convert_keys_to_camel_case( $input );
			}
			$inputs_converted[ $key ] = $input;
		}

		return $inputs_converted;
	}

endif;

if ( ! function_exists( __NAMESPACE__ . '\\convert_keys_to_snake_case' ) ) :

	/**
	 * Function to transform the array keys to snake_case.
	 *
	 * This function will be used to convert associative array that
	 * will be used in PHP.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $inputs Associative array.
	 * @return array Associative array with the key converted to camelcase
	 */
	function convert_keys_to_snake_case( array $inputs ) {

		$inputs_converted = [];
		foreach ( $inputs as $key => $input ) {
			$key = from_camel_to_snake( $key );
			if ( is_array( $input ) ) {
				$input = convert_keys_to_snake_case( $input );
			}
			$inputs_converted[ $key ] = $input;
		}

		return $inputs_converted;
	}

endif;
