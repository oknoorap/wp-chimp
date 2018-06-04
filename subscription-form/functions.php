<?php
/**
 * File to save the Plugin Core functions
 *
 * @since   0.1.0
 * @package WP_Chimp\includes
 */

namespace WP_Chimp\Subscription_Form;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use WP_REST_Request;
use WP_Chimp\Includes\Utilities;

/**
 * Function to get the list using the WP REST API.
 *
 * @since 0.1.0
 *
 * @return array;
 */
function get_lists() {

	$request = new WP_REST_Request( 'GET', '/wp-chimp/v1/lists' );
	$request->set_query_params( [
		'context' => 'block',
	] );

	$response = rest_do_request( $request );
	$data     = $response->get_data();

	return Utilities\convert_keys_to_snake_case( $data );
}

/**
 * Get the default MailChimp list.
 *
 * @since 0.1.0
 *
 * @return string
 */
function get_default_list() {

	$default = [];
	$lists   = get_lists();

	if ( 1 <= count( $lists ) ) {
		$default = $lists[0]['list_id'];
	}

	return $default;
}

/**
 * Function to define translatable strings to diplay in the "Subscription Form".
 *
 * @since 0.1.0
 *
 * @return array Lists of translatable strings.
 */
function get_locale_strings() {

	return [
		'title'                   => __( 'Subscription Form', 'wp-chimp' ),
		'description'             => __( 'Display a MailChimp subscription form.', 'wp-chimp' ),
		'heading_text'            => __( 'Subscribe', 'wp-chimp' ),
		'sub_heading_text'        => __( 'Get notified of our next update right to your inbox', 'wp-chimp' ),
		'input_email_placeholder' => __( 'Enter your email address', 'wp-chimp' ),
		'button_text'             => __( 'Submit', 'wp-chimp' ),
	];
}

/**
 * Function to transform the array keys to camelCase.
 *
 * This function will be used to convert associative array that
 * will be used in JavaScript.
 *
 * @since 0.1.0
 *
 * @param  array $attrs The attributes to assign to the .
 * @return null|string Associative array with the key converted to camelcase,
 *                     otherwise 'null' if the list ID is not present.
 */
function render( array $attrs ) {

	if ( ! is_string( $attrs['list_id'] ) || empty( $attrs['list_id'] ) ) {
		return;
	}

	$action_url = 'wp-chimp/v1/lists/' . $attrs['list_id'];

	ob_start();
	?>

	<div class="wp-chimp-subscription-form" method="post" action="<?php echo rest_url( $action_url ); ?>">
		<h3 class="wp-chimp-subscription-form__heading"><?php echo esc_html( $attrs['heading_text'] ); ?></h3>
		<p class="wp-chimp-subscription-form__sub-heading"><?php echo esc_html( $attrs['sub_heading_text'] ); ?></p>
		<form class="wp-chimp-subscription-form__inputs">
			<input class="wp-chimp-subscription-form__email-field" name="email" type="email" placeholder="<?php echo esc_html( $attrs['input_email_placeholder'] ); ?>">
			<button class="wp-chimp-subscription-form__button"><?php echo esc_html( $attrs['button_text'] ); ?></button>
		</form>
	</div>

	<?php
	$subscription_form = ob_get_contents();
	ob_end_clean();

	return $subscription_form;
}
