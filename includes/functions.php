<?php
/**
 * Functions, Utilities, and Helpers
 *
 * @since   0.1.0
 * @package WP_Chimp\includes
 */

namespace WP_Chimp;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
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
function sort_mailchimp_data( $raw_data ) {
	$data = [];
	return $data;
}

/**
 * Function to sanitize the data of MailChimp API.
 *
 * This function is typically used be used for sanitize data
 * before adding them to the database.
 *
 * @since  0.1.0
 * @see    WP_Chimp\soret_mailchimp_data
 *
 * @param  array $data The sorted out data of MailChimp API unsantized.
 * @return array The data sanitized
 */
function sanitize_mailchimp_data( $data ) {
	return $data;
}
