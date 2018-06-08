<?php
/**
 * File to save the Plugin Core functions
 *
 * @since   0.1.0
 * @package WP_Chimp\includes
 */

namespace WP_Chimp\Subscription_Form;

if ( ! defined( 'ABSPATH' ) ) { // If this file is called directly, abort.
	die;
}

use WP_REST_Request;
use WP_Chimp\Includes\Utilities;

/**
 * Function to get the lists using WP REST API.
 *
 * @since 0.1.0
 *
 * @return array
 */
function get_the_lists() {

	$request = new WP_REST_Request( 'GET', '/wp-chimp/v1/lists' );
	$request->set_query_params( [
		'context' => 'block',
	] );

	$response = rest_do_request( $request );
	$data     = $response->get_data();

	return Utilities\convert_keys_to_snake_case( $data );
}

/**
 * Function to get the default MailChimp list.
 *
 * The default is currently picked up of the first one
 * on the list.
 *
 * @since 0.1.0
 *
 * @return string
 */
function get_the_default_list() {

	$default = [];
	$lists   = get_the_lists();

	if ( 1 <= count( $lists ) ) {
		$default = $lists[0]['list_id'];
	}

	return $default;
}

/**
 * Function to get the translatable strings to diplay on the "Subscription Form".
 *
 * @since 0.1.0
 *
 * @return array The list of translateable strings.
 */
function get_the_locale_strings( $key = '' ) {

	$locale = [
		'title' => __( 'Subscription Form', 'wp-chimp' ),
		'description' => __( 'Display a MailChimp subscription form.', 'wp-chimp' ),
		'heading_text' => __( 'Subscribe', 'wp-chimp' ),
		'sub_heading_text' => __( 'Get notified of our next update right to your inbox', 'wp-chimp' ),
		'input_email_placeholder' => __( 'Enter your email address', 'wp-chimp' ),
		'button_text' => __( 'Submit', 'wp-chimp' ),

		// translators: %1$s the MailChimp List knowledgebase link URL, %2$s the "Chimp" setting page.
		'inactive_notice' => sprintf( __( 'Subscription Form is inactive. You might haven\'t yet input the MailChimp API key to %1$s or your MailChimp account might not contain a %2$s.', 'wp-chimp' ), '<a href="' . admin_url( 'options-general.php?page=wp-chimp' ) . '" target="_blank">' . __( 'the Settings page', 'wp-chimp' ) . '</a>', '<a href="https://kb.mailchimp.com/lists" target="_blank">' . __( 'List', 'wp-chimp' ) . '</a>' ),
	];

	return isset( $locale[ $key ] ) ? $locale[ $key ] : $locale;
}

/**
 * Undocumented function
 *
 * @since 0.1.0
 *
 * @return array
 */
function get_the_default_attrs() {

	return [
		'list_id' => get_the_default_list(),
		'heading_text' => get_the_locale_strings( 'heading_text' ),
		'sub_heading_text' => get_the_locale_strings( 'sub_heading_text' ),
		'input_email_placeholder' => get_the_locale_strings( 'input_email_placeholder' ),
		'button_text' => get_the_locale_strings( 'button_text' ),
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

	<div class="wp-chimp-subscription-form" method="post" action="<?php echo esc_attr( rest_url( $action_url ) ); ?>">
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
