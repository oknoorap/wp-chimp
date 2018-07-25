<?php
/**
 * Subscription Form: Shortcode class
 *
 * @package WP_Chimp/Subscription_Form
 * @since 0.1.0
 */

namespace WP_Chimp\Subscription_Form;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Class to define the "Subscription Form" shortcode.
 *
 * @since 0.1.0
 */
final class Shortcode {

	/**
	 * Render the Subscription Form HTML output defined from the shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @param array $attrs The shortcode attributes.
	 * @return string The "Subscription Form" HTML.
	 */
	public static function render( $attrs ) {
		return render( $attrs );
	}
}
