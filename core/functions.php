<?php
/**
 * File to save the Plugin Core functions
 *
 * @package WP_Chimp/Core
 * @since 0.1.0
 */

namespace WP_Chimp\Core;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Retrieve the option value with the default fallback.
 *
 * @since 0.2.0
 *
 * @param string $option_name The option name. It must be the one recognized in the plugin.
 * @return WP_Error|mixed The option value. WP_Error if the option name is not recognized.
 */
function get_the_option( $option_name ) {
	return Options::get( $option_name );
}

/**
 * Update the value of an option that was already added.
 *
 * @since 0.2.0
 *
 * @param string $option_name (Required) Name of option to update. It must be the one recognized in the plugin.
 * @param string $value Option value.
 * @return WP_Error|boolean False if value was not updated, otherwise true. WP_Error if the option name is not recognized.
 */
function update_the_option( $option_name, $value ) {
	return Options::update( $option_name, $value );
}

/**
 * Add a new option.
 *
 * @since 0.2.0
 *
 * @param string $option_name (Required) Name of option to update. It must be the one recognized in the plugin.
 * @param string $value Option value.
 * @return WP_Error|boolean False if value was not updated, otherwise true. WP_Error if the option name is not recognized.
 */
function add_the_option( $option_name, $value ) {
	return Options::add( $option_name, $value );
}

/**
 * Retrieve the MailChimp API key.
 *
 * @since 0.1.0
 *
 * @return string The MailChimp API key or an empty string.
 */
function get_the_mailchimp_api_key() {
	return get_the_option( 'wp_chimp_api_key' );
}

/**
 * Function to retrieve the API key status.
 *
 * If MailChimp returns an error when fetching the data from the API,
 * this option is set to `invalid`.
 *
 * @since 0.1.0
 *
 * @return bool Returns `true` if the MailChimp API key is a valid key
 *              else `false`.
 */
function get_the_mailchimp_api_key_status() {

	$api_key_status = get_the_option( 'wp_chimp_api_key_status' );
	return 'invalid' === $api_key_status ? false : true;
}

/**
 * Retrieve the number of mail lists on the MailChimp account.
 *
 * The total items are retrieved from the MailChimp API response when
 * the API key is added or when the data is resynced.
 *
 * @since 0.1.0
 *
 * @return int The Lists total items.
 */
function get_the_lists_total_items() {

	$total_items = get_the_option( 'wp_chimp_lists_total_items' );
	return absint( $total_items );
}

/**
 * Set the default of the MailChimp lists.
 *
 * @since 0.2.0
 *
 * @param array $lists The MailChimp lists data.
 * @param array $index The index on the array in the MailChimp lists to set as the default.
 * @return void
 */
function set_the_default_list( array $lists, $index = 0 ) {

	if ( isset( $lists[ $index ] ) && isset( $lists[ $index ]['list_id'] ) ) {
		$default = (string) $lists[ $index ]['list_id'];
		update_the_option( 'wp_chimp_lists_default', $default );
	}
}

/**
 * Retrieve the default list from database.
 *
 * @since 0.2.0
 *
 * @return string The ID of the default list.
 */
function get_the_default_list() {
	return get_the_option( 'wp_chimp_lists_default' );
}

/**
 * Check whether the lists are already installed to the database.
 *
 * This is set when the "Background Process" is done installing each list.
 *
 * @since 0.1.0
 *
 * @return boolean
 */
function is_lists_init() {

	$init = get_the_option( 'wp_chimp_lists_init' );
	return 1 === absint( $init );
}

/**
 * Check the MailChimp API status.
 *
 * The function check whether the MailChimp API key is set and present,
 * it's a valid API key, and there's at least 1 mail list in the
 * account. If these all are met, we can safely assume that
 * the MailChimp API is valid.
 *
 * @since 0.1.0
 *
 * @return bool Returns `true` if all conditions are met, otherwise `false`
 */
function is_mailchimp_api_valid() {

	$api_key = (bool) get_the_mailchimp_api_key();
	$api_key_status = get_the_mailchimp_api_key_status();
	$total_items = get_the_lists_total_items();

	return $api_key && $api_key_status && 0 < $total_items;
}

/**
 * Retrieve the WP-Chimp REST API base/namespace.
 *
 * @since 0.1.0
 *
 * @return string The WP-Chimp REST API endpont base URL.
 */
function get_the_rest_api_namespace() {
	return Endpoints\REST_Lists_Controller::REST_NAMESPACE;
}

/**
 * Retrieve the WP-Chimp REST API base URL.
 *
 * @since 0.1.0
 *
 * @return string The full URL of the WP-Chimp REST API endpoint.
 */
function get_the_rest_api_url() {
	return rest_url( get_the_rest_api_namespace() );
}

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

		$list_id = isset( $list['id'] ) ? $list['id'] : '';
		$list_name = isset( $list['name'] ) ? $list['name'] : '';
		$list_subscribers = isset( $list['stats'] ) && isset( $list['member_count'] ) ? $list['stats'] : [];
		$list_optin = isset( $list['double_optin'] ) ? $list['double_optin'] : 0;

		if ( ! empty( $list_id ) ) {
			$sorted_data[ $key ] = [
				'list_id' => sanitize_key( $list['id'] ),
				'name' => sanitize_text_field( $list['name'] ),
				'subscribers' => absint( $list['stats']['member_count'] ),
				'double_optin' => true === $list['double_optin'] ? 1 : 0,
			];
		}
	}

	return $sorted_data;
}

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

/**
 * Convert string from camelCase to snake_case
 *
 * @since 0.1.0
 *
 * @param string $string The string to convert in camelCase format.
 * @return string The converted string in snake_case
 */
function from_camel_to_snake( $string ) {
	return strtolower( preg_replace( [ '/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/' ], '$1_$2', $string ) );
}

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

/**
 * Obfuscate half of a string.
 *
 * @since 0.1.0
 *
 * @param string $string The API key string.
 * @return string The obfuscated API key
 */
function obfuscate_string( $string = '' ) {

	$obfuscated_api_key = '';

	if ( is_string( $string ) && ! empty( $string ) ) {

		$api_key_length = strlen( $string );
		$obfuscated_length = ceil( $api_key_length / 2 );
		$obfuscated_api_key = str_repeat( '*', $obfuscated_length ) . substr( $string, $obfuscated_length );
	}

	return $obfuscated_api_key;
}

/**
 * Validate and convert a value into a string type of value.
 *
 * @since 0.2.0
 *
 * @param mixed $value The value to filter.
 * @return string
 */
function filter_string( $value ) {
	return filter_var( $value, FILTER_SANITIZE_STRING );
}

/**
 * Validate API key status output.
 *
 * @since 0.2.0
 *
 * @param mixed $value The value to filter.
 * @return string
 */
function filter_api_key_status( $value ) {

	if ( in_array( $value, [ 'valid', 'invalid' ], true ) ) {
		return $value;
	}

	return 'invalid';
}
