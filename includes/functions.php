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

if ( ! function_exists( __NAMESPACE__ . '\\get_subscription_form_locale' ) ) :

	/**
	 * Function to return default translatable strings for the "Subscribe Form"
	 *
	 * @since 0.1.0
	 *
	 * @return array Lists of translatable strings.
	 */
	function get_subscription_form_locale() {

		return [
			'title'                   => __( 'Subscription Form', 'wp-chimp' ),
			'description'             => __( 'Display a MailChimp subscription form.', 'wp-chimp' ),
			'heading_text'            => __( 'Subscribe', 'wp-chimp' ),
			'sub_heading_text'        => __( 'Get notified of our next update right to your inbox', 'wp-chimp' ),
			'input_email_placeholder' => __( 'Enter your email address', 'wp-chimp' ),
			'button_text'             => __( 'Submit' ),
		];
	}

endif;

if ( ! function_exists( __NAMESPACE__ . '\\render_subscription_form' ) ) :

	/**
	 * Function to transform the array keys to camelCase.
	 *
	 * This function will be used to convert associative array that
	 * will be used in JavaScript.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $attributes The attributes to assign to the .
	 * @return null|string Associative array with the key converted to camelcase,
	 *                     otherwise 'null' if the list ID is not present.
	 */
	function render_subscription_form( array $attributes, $before = '', $after = '' ) {

		$defaults = array_merge( [
			'list_id' => '',
		], get_subscription_form_locale() );

		$attributes = Utilities\convert_keys_to_snake_case( $attributes );
		$attributes = wp_parse_args( $attributes, $defaults );

		if ( ! is_string( $attributes['list_id'] ) || empty( $attributes['list_id'] ) ) {
			return;
		}
		ob_start();
		?>

		<div class="wp-chimp-subscription-form" data-list-id="<?php echo esc_attr( $attributes['list_id'] ); ?>">
			<h3 class="wp-chimp-subscription-form__heading"><?php echo esc_html( $attributes['heading_text'] ); ?></h3>
			<p class="wp-chimp-subscription-form__sub-heading"><?php echo esc_html( $attributes['sub_heading_text'] ); ?></p>
			<div class="wp-chimp-subscription-form__inputs">
				<input class="wp-chimp-subscription-form__email-field" type="email" placeholder="<?php echo esc_html( $attributes['input_email_placeholder'] ); ?>">
				<button class="wp-chimp-subscription-form__button"><?php echo esc_html( $attributes['button_text'] ); ?></button>
			</div>
		</div>

		<?php
		$subscription_form = ob_get_contents();
		ob_end_clean();

		return $before . $subscription_form . $after;
	}

endif;
