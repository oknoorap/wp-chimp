<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link https://wp-chimp.com
 * @package WP_Chimp
 * @since 0.1.0
 *
 * @wordpress-plugin
 * Plugin Name: WP Chimp
 * Plugin URI: https://wordpress.org/plugins/wp-chimp
 * Description: Lean MailChimp subscription form plugin for WordPress
 * Version: 0.2.1
 * Author: Thoriq Firdaus
 * Author URI: https://wp-chimp.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-chimp
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Load the autoloaders that will automatically include the appropriate file
 * when a Class is instantiated. The `vendor/autoload.php` specifically
 * will load files from the packages installed through Composer
 *
 * @link http://php.net/manual/en/function.spl-autoload-register.php
 */
require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * This function is also useful to check if the plugin is activated
 * through the function_exists() function.
 *
 * @since  0.1.0
 *
 * @return WP_Chimp\Core\Plugin The Plugin instance.
 */
function wp_chimp() {

	static $plugin;

	if ( is_null( $plugin ) ) {

		$plugin = new WP_Chimp\Core\Plugin( 'wp-chimp', '0.2.1', __FILE__ );
		$plugin->set_loader( new WP_Chimp\Core\Loader() );

		$plugin->run();
	}

	return $plugin;
}
wp_chimp();
