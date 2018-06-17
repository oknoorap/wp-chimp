<?php
/**
 * File to save the Plugin Core functions
 *
 * @since 0.1.0
 * @package WP_Chimp\Subscription_Form\Functions
 */

namespace WP_Chimp\Includes;

/* If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Retrieve the MailChimp API key.
 *
 * @since 0.1.0
 *
 * @return string The MailChimp API key or an empty string.
 */
function get_the_mailchimp_api_key() {
	return get_option( 'wp_chimp_api_key', '' );
}

/**
 * Function to retrieve the API key status.
 *
 * If MailChimp returns an error when fetching the data from the API,
 * this option is set to `invalid`.
 *
 * @since 0.1.0
 * @see Admin\Page()->updated_option();
 *
 * @return bool Returns `true` if the MailChimp API key is a valid key
 *              else `false`.
 */
function get_the_mailchimp_api_key_status() {

	$api_key_status = (string) get_option( 'wp_chimp_api_key_status', 'invalid' );
	return 'invalid' === $api_key_status ? false : true;
}

/**
 * Retrieve the number of mail lists on the MailChimp account.
 *
 * The total items are retrieved from the MailChimp API response when
 * the API key is added or when the data is resynced.
 *
 * @since 0.1.0
 * @see Admin\Page()->updated_option();
 *
 * @return int The Lists total items.
 */
function get_the_lists_total_items() {

	$total_items = get_option( 'wp_chimp_lists_total_items', 0 );
	return absint( $total_items );
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
	return Endpoints\REST_Lists_Controller::get_namespace();
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
