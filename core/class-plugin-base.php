<?php
/**
 * The file that defines the abstract of the plugin base Class
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
 * The plugin base abstract class.
 *
 * @since 0.3.0
 *
 * @property WP_Chimp\Core\Loader $loader
 * @property string $plugin_name
 * @property string $file_path
 * @property string $version
 */
abstract class Plugin_Base {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 0.3.0
	 * @var WP_Chimp\Core\Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 0.3.0
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The filename of plugin.
	 *
	 * This is used for WordPress functions requiring the path to the main plugin file,
	 * such as `plugin_dir_path()` and `plugin_basename()`.
	 *
	 * @since 0.3.0
	 * @var string
	 */
	protected $file_path;

	/**
	 * The current version of the plugin.
	 *
	 * @since 0.3.0
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The Constructor
	 *
	 * @since 0.3.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @param string $file_path   The full path of the main plugin file.
	 */
	public function __construct( $plugin_name, $version, $file_path = '' ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->file_path = $file_path;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.3.0
	 */
	abstract public function run();

	/**
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 0.3.0
	 *
	 * @param Loader $loader The Loader instance.
	 */
	public function set_loader( Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 0.3.0
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 0.3.0
	 *
	 * @return WP_Chimp\Core\Loader The Loader instance.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 0.3.0
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the plugin file path.
	 *
	 * @since 0.3.0
	 *
	 * @return string The plugin file path.
	 */
	public function get_file_path() {
		return $this->file_path;
	}
}
