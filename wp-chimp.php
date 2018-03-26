<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://wp-chimp.com
 * @since   0.1.0
 * @package WP_Chimp
 *
 * @wordpress-plugin
 * Plugin Name: WP Chimp
 * Plugin URI: https://wordpress.org/plugins/wp-chimp
 * Description: This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version: 0.1.0
 * Author: Thoriq Firdaus
 * Author URI: https://wp-chimp.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-chimp
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Load packages installed through Composer.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin.php';

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
 * @return WP_Chimp\Plugin The Plugin instance.
 */
function wp_chimp() {

	static $plugin;

	if ( is_null( $plugin ) ) {
		$plugin = new WP_Chimp\Plugin( 'wp-chimp', '0.1.0', __FILE__ );
		$plugin->run();
	}

	return $plugin;
}
wp_chimp();
