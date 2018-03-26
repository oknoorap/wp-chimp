<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wp-chimp.com
 * @since             0.1.0
 * @package           WP_Chimp
 *
 * @wordpress-plugin
 * Plugin Name:       WP-Chimp
 * Plugin URI:        https://wordpress.org/plugins/wp-chimp
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           0.1.0
 * Author:            Thoriq Firdaus
 * Author URI:        https://wp-chimp.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-chimp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently pligin version.
 * Start at version 0.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_CHIMP_VERSION', '0.1.0' );

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
 * @since    0.1.0
 */
function run_wp_chimp() {

	$args = [
		'plugin_name' => 'wp-chimp',
		'plugin_file' => __FILE__,
		'version'     => WP_CHIMP_VERSION,
	];

	$plugin = new WP_Chimp\Plugin( $args );
	$plugin->run();
}
run_wp_chimp();
