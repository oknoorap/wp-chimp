<?php
/**
 * WP_Chimp Autoloader.
 *
 * @package WP_Chimp
 * @since 0.1.0
 */

namespace WP_Chimp;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Autoloader for Classes within the WP_Chimp namespace.
 *
 * @param string $class_name The loaded class name e.g. WP_Chimp\Class_Name.
 */
function autoloader( $class_name ) {

	// If the specified $class_name does not include our namespace, duck out.
	if ( false === strpos( $class_name, 'WP_Chimp' ) ) {
		return;
	}

	// Split the class name into an array to read the namespace and class.
	$file_parts = explode( '\\', $class_name );

	// Do a reverse loop through $file_parts to build the path to the file.
	$namespace = '';
	for ( $i = count( $file_parts ) - 1; $i > 0; $i-- ) {

		// Read the current component of the file part.
		$current = strtolower( $file_parts[ $i ] );
		$current = str_ireplace( '_', '-', $current );

		// If we're at the first entry, then we're at the filename.
		if ( count( $file_parts ) - 1 === $i ) {

			/**
			 * If 'interface' is contained in the parts of the file name, then
			 * define the $file_name differently so that it's properly loaded.
			 * Otherwise, just set the $file_name equal to that of the class
			 * filename structure.
			 */
			if ( strpos( strtolower( $file_parts[ count( $file_parts ) - 1 ] ), 'interface' ) ) {

				// Grab the name of the interface from its qualified name.
				$interface_name = explode( '_', $file_parts[ count( $file_parts ) - 1 ] );
				$interface_name = $interface_name[0];

				$file_name = "interface-$interface_name.php";
			} else {
				$file_name = "class-$current.php";
			}
		} else {
			$namespace = '/' . $current . $namespace;
		}
	}

	// Now build a path to the file using mapping to the file location.
	$filepath  = trailingslashit( dirname( __FILE__ ) . $namespace );
	$filepath .= $file_name;

	// If the file exists in the specified path, then include it.
	if ( file_exists( $filepath ) ) {
		include_once $filepath;
	} else {

		// Translators: the file path of the class to load.
		$message = __( 'The file attempting to be loaded at %s does not exist.', 'wp-chimp' );
		wp_die(
			wp_kses( sprintf( $message, "<code>${filepath}</code>" ), [ 'code' => true ] )
		);
	}
}

spl_autoload_register( __NAMESPACE__ . '\\autoloader' );
