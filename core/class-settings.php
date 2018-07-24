<?php
/**
 * The file that defines the handle the plugin Settings and State
 *
 * Primarily, those settings that are consumable on the front-end.
 *
 * @package WP_Chimp/Core
 * @since 0.3.0
 */

namespace WP_Chimp\Core;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Define the class that handle the plugin Settings and State.
 *
 * @since 0.3.0
 */
class Settings extends Plugin_Base {

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.3.0
	 */
	public function run() {
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_state', 30 );
	}

	/**
	 * Function to add the settings state.
	 *
	 * The settings state will be used in the JavaScript side of the plugin
	 * i.e. whether we should display the 'Subscription Form', request data
	 * to MailChimp API, etc.
	 *
	 * @since 0.3.0
	 * @see ./admin/js/admin.es
	 * @see ./admin/js/utilities.es
	 */
	public function enqueue_state() {

		$state = self::get_setting_state();
		$data = 'var wpChimpSettingState = ' . wp_json_encode( $state );

		wp_add_inline_script( $this->plugin_name, $data, 'before' );
		wp_add_inline_script( 'wp-chimp-subscription-form-editor', $data, 'before' );
	}

	/**
	 * Retrieve options and nonces.
	 *
	 * This data will be primarily consumed in the JavaScript.
	 *
	 * @since 0.3.0
	 * @see enqueue_state
	 *
	 * @return array
	 */
	protected static function get_state() {

		$args = [
			'nonce' => wp_create_nonce( 'wp-chimp-setting' ),
			'wp_rest_nonce' => wp_create_nonce( 'wp_rest' ),
			'rest_api_url' => get_the_rest_api_url(),
			'mailchimp_api_status' => is_mailchimp_api_valid(),
			'lists_total_items' => get_the_lists_total_items(),
			'lists_init' => is_lists_init(),
		];

		return convert_keys_to_camel_case( $args );
	}
}
