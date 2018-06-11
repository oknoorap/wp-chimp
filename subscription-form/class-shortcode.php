<?php
/**
 * File containing the Class to define the "Subscribe Form" Shortcode
 *
 * @since 0.1.0
 * @package WP_Chimp
 * @subpackage WP_Chimp/Subscription_Form
 */

namespace WP_Chimp\Subscription_Form;

/* If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Class to define the "Subscription Form" Widget.
 *
 * @since 0.1.0
 * @author Thoriq Firdaus <thoriqoe@gmail.com>
 */
final class Shortcode {

	/**
	 * Render the "Subscription Form" HTML output of shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @param array $attrs The shortcode attributes.
	 * @return string The "Subscription Form" HTML markup.
	 */
	public static function render( $attrs ) {
		return render( $attrs );
	}
}
