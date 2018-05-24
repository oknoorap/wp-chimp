<?php
/**
 * File to save the Plugin Core functions
 *
 * @since   0.1.0
 * @package WP_Chimp\includes
 */

namespace WP_Chimp\Includes\Functions;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly, abort.
	die;
}

use WP_Chimp\Includes\Utilities;

if ( ! function_exists( __NAMESPACE__ . '\\render_subscribe_form' ) ) :

	/**
	 * Function to transform the array keys to camelCase.
	 *
	 * This function will be used to convert associative array that
	 * will be used in JavaScript.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $attributes The Block attributes.
	 * @return null|string Associative array with the key converted to camelcase,
	 *                     otherwise 'null' if the list ID is not present.
	 */
	function render_subscribe_form( array $attributes ) {

		$attributes = Utilities\convert_keys_to_snake_case( $attributes );
		$attributes = wp_parse_args( $attributes, [
			'list_id' => '',
			'heading_text' => __( 'Subscribe to our newsletter', 'wp-chimp' ),
			'sub_heading_text' => __( 'Get notified of our next update right to your inbox', 'wp-chimp' ),
			'input_email_placeholder' => __( 'Enter your email address', 'wp-chimp' ),
			'button_text' => __( 'Subscribe', 'wp-chimp' )
		]);

		if ( ! is_string( $attributes['list_id'] ) || empty( $attributes['list_id'] ) ) {
			return;
		}

		ob_start();
		?>

		<div class="wp-chimp-block wp-chimp-subscribe-form" data-list-id="<?php echo esc_attr( $attributes['list_id'] ); ?>">
			<h3 class="wp-chimp-subscribe-form__heading"><?php echo esc_html( $attributes['heading_text'] ) ?></h3>
			<p class="wp-chimp-subscribe-form__sub-heading"><?php echo esc_html( $attributes['sub_heading_text'] ) ?></p>
			<div class="wp-chimp-subscribe-form__inputs">
				<input class="wp-chimp-subscribe-form__email-field" type="email" placeholder="<?php echo esc_html( $attributes['input_email_placeholder'] ) ?>">
				<button class="wp-chimp-subscribe-form__button"><?php echo esc_html( $attributes['button_text'] ) ?></button>
			</div>
		</div>

		<?php ;
		$subscription_form = ob_get_contents();
		ob_end_clean();

		return $subscription_form;
	}

endif;
