<?php
/**
 * File containing the Class to register all the widgets.
 *
 * @package WP_Chimp
 * @subpackage WP_Chimp/widgets
 */

namespace WP_Chimp\Widgets;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

namespace WP_Chimp\Widgets;

/**
 * Class to register the widgets.
 *
 * @since  0.1.0
 * @author Thoriq Firdaus <thoriqoe@gmail.com>
 */
final class Widgets {

	/**
	 * Function to register the widgets.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() {

		register_widget( __NAMESPACE__ . '\\Subscribe_Form' );
	}
}
