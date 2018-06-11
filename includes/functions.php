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

use WP_Chimp\Includes\Endpoints;

/**
 * Function to retrieve the MailChimp API key saved.
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
 * @see Admin\Page\updated_option();
 *
 * @return bool Returns `true` if the MailChimp API key is a valid key
 *              else `false`.
 */
function get_the_mailchimp_api_key_status() {
	$api_key_status = (string) get_option( 'wp_chimp_api_key_status', 'invalid' );
	return 'invalid' === $api_key_status ? false : true;
}

/**
 * Function to retrive the number of lists retrieved from the MailChimp accounts.
 *
 * @since 0.1.0
 *
 * @return int The lists total items.
 */
function get_the_lists_total_items() {
	$total_items = get_option( 'wp_chimp_lists_total_items', 0 );
	return absint( $total_items );
}

/**
 * Function to retrieve the MailChimp API status.
 *
 * @since 0.1.0
 *
 * @return int
 */
function get_the_mailchimp_api_status() {

	$api_key = (bool) get_the_mailchimp_api_key();
	$api_key_status = get_the_mailchimp_api_key_status();

	return $api_key && $api_key_status;
}

/**
 * Function to retrieve the WP-Chimp REST API base/namespace.
 *
 * @since 0.1.0
 *
 * @return string
 */
function get_the_rest_api_namespace() {
	return Endpoints\REST_Lists_Controller::get_namespace();
}

/**
 * Function to retrieve the WP-Chimp REST API base URL.
 *
 * @since 0.1.0
 *
 * @return string
 */
function get_the_rest_api_url() {
	return rest_url( get_the_rest_api_namespace() );
}
