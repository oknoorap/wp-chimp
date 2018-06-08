<?php

/**
 * Undocumented function
 *
 * @return void
 */
function get_the_mailchimp_api_key() {
	return get_option( 'wp_chimp_api_key', '' );
}

/**
 * Undocumented function
 *
 * @return void
 */
function get_the_mailchimp_api_key_status() {
	$api_key_status = (string) get_option( 'wp_chimp_api_key_status', 'invalid' );
	return 'invalid' === $api_key_status ? false : true;
}

/**
 * Undocumented function
 *
 * @return void
 */
function get_the_lists_total_items() {
	return (int) get_option( 'wp_chimp_lists_total_items', 0 );
}

/**
 * Undocumented function
 *
 * @return void
 */
function get_the_mailchimp_api_status() {

	$api_key = (bool) get_the_mailchimp_api_key();
	$api_key_status = get_the_mailchimp_api_key_status();

	return ! $api_key || ! $api_key_status;
}
