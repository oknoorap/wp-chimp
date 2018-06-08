<?php

namespace WP_Chimp\Subscription_Form;

/**
 * Undocumented class
 */
final class Shortcode {

	/**
	 * Render the "Subscription Form" HTML from the shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @param array $attrs The shortcode attributes.
	 * @return string The "Subscription Form" HTML markup.
	 */
	public static function render( $attrs ) {

		$attrs = wp_parse_args( $attrs, get_the_default_attrs() );

		return render( $attrs );
	}
}
