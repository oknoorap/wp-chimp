<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since 0.1.0
 * @package WP_Chimp/Core
 */

namespace WP_Chimp\Core;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since 0.1.0
 *
 * @property string $plugin_name
 * @property string $file_path
 * @property string $version
 */
class Languages {

	// Text domain. Unique identifier for retrieving translated strings.
	const DOMAIN = 'wp-chimp';

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 0.1.0
	 *
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The filename of plugin.
	 *
	 * This is used for WordPress functions requiring the path to the main plugin file,
	 * such as `plugin_dir_path()` and `plugin_basename()`.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	protected $file_path;

	/**
	 * The current version of the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @param string $file_path   The full path of the main plugin file.
	 */
	public function __construct( $plugin_name, $version, $file_path ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->file_path = $file_path;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.1.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( self::DOMAIN, false, dirname( plugin_basename( $this->file_path ) ) . '/languages/' );
	}
}
