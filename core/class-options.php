<?php
/**
 * Core: Options class
 *
 * @package WP_Chimp\Core
 * @since 0.2.0
 */

namespace WP_Chimp\Core;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Error;

/**
 * Define the site options functionality.
 *
 * The class poses a wrapper for the native WordPress `*_option` functions.
 *
 * @since 0.2.0
 */
class Options {

	/**
	 * Lists of the registered options in the plugin; the names and their default value.
	 *
	 * @since 0.2.0
	 *
	 * @var array
	 */
	static public $options = [
		'wp_chimp_api_key' => [
			'default' => '',
			'sanitize_callback' => 'WP_Chimp\\Core\\filter_string',
		],
		'wp_chimp_lists_default' => [
			'default' => '',
			'sanitize_callback' => 'WP_Chimp\\Core\\filter_string',
		],
		'wp_chimp_api_key_status' => [
			'default' => 'invalid',
			'sanitize_callback' => 'WP_Chimp\\Core\\filter_api_key_status',
		],
		'wp_chimp_lists_total_items' => [
			'default' => 0,
			'sanitize_callback' => 'absint',
		],
		'wp_chimp_lists_init' => [
			'default' => 0,
			'sanitize_callback' => 'absint',
			'autoload' => false,
		],
	];

	/**
	 * Add option and the default value if it does not exsit
	 *
	 * @since 0.2.0
	 */
	public static function ensure_options() {

		foreach ( self::$options as $option_name => $data ) {
			if ( false === get_option( $option_name ) ) {
				$autoload = isset( $data['autoload'] ) ? $data['autoload'] : true;
				add_option( $option_name, $data['default'], '', $autoload );
			}
		}
	}

	/**
	 * Retrieve the option value with the default fallback.
	 *
	 * @since 0.2.0
	 *
	 * @param string $option_name The option name. It must be the one recognized in the plugin.
	 * @return WP_Error|mixed The option value. WP_Error if the option name is not recognized.
	 */
	public static function get( $option_name ) {

		if ( isset( self::$options[ $option_name ] ) ) {
			$value = get_option( $option_name, self::$options[ $option_name ]['default'] );
			return call_user_func( self::$options[ $option_name ]['sanitize_callback'], $value );
		} else {
			return new WP_Error( 'wp-chimp-option-name-invalid', __( 'The option name is not registered.', 'wp-chimp' ) );
		}
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
	public static function update( $option_name, $value ) {

		if ( isset( self::$options[ $option_name ] ) ) {
			return update_option( $option_name, $value );
		} else {
			return new WP_Error( 'wp-chimp-option-name-invalid', __( 'The option name is not registered.', 'wp-chimp' ) );
		}
	}

	/**
	 * Add a new option.
	 *
	 * This method is simply a wrapper to the `update_option` function,
	 * to prevent adding the same option multiple times, accidentally.
	 *
	 * @since 0.2.0
	 *
	 * @param string $option_name (Required) Name of option to update. It must be the one recognized in the plugin.
	 * @param string $value Option value.
	 * @return WP_Error|boolean False if value was not updated, otherwise true. WP_Error if the option name is not recognized.
	 */
	public static function add( $option_name, $value ) {
		return self::update( $option_name, $value );
	}
}
