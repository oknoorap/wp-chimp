<?php
/**
 * File containing the core functions used in the "Subscription Form"
 *
 * @since   0.1.0
 * @package WP_Chimp\Subscription_Form
 */

namespace WP_Chimp\Subscription_Form;

/* If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_REST_Request;
use WP_Chimp\Includes;
use WP_Chimp\Includes\Utilities;

/**
 * Retrieve the MailChimp lists using WP REST API.
 *
 * @since 0.1.0
 *
 * @return array The MailChimp lists (array keys are converted to snake_case).
 */
function get_the_lists() {

	$request = new WP_REST_Request( 'GET', '/wp-chimp/v1/lists' );
	$request->set_query_params( [
		'context' => 'block',
	] );

	$response = rest_do_request( $request );
	$data = $response->get_data();

	return Utilities\convert_keys_to_snake_case( $data );
}

/**
 * Retrieve the MailChimp lists count
 *
 * It's meant to count the list the actual number of list items retrieved
 * from the database.
 *
 * @since 0.1.0
 *
 * @return int The list count number.
 */
function get_the_lists_count() {

	$lists = get_the_lists();
	$lists_count = count( $lists );

	return absint( $lists_count );
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
	$lists = get_the_lists();

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
 * @param string $key The array key to retrieve a specific locale string.
 * @return array|string The list of translateable strings or a string if the `$key` is defined.
 */
function get_the_locale_strings( $key = '' ) {

	$locale = [
		'title' => __( 'Subscription Form', 'wp-chimp' ),
		'description' => __( 'Display a MailChimp subscription form.', 'wp-chimp' ),

		// Default text in the "Subscription Form".
		'heading_text' => __( 'Subscribe', 'wp-chimp' ),
		'sub_heading_text' => __( 'Get notified of our next update right to your inbox', 'wp-chimp' ),
		'email_placeholder_text' => __( 'Enter your email address', 'wp-chimp' ),
		'button_text' => __( 'Submit', 'wp-chimp' ),
		'footer_text' => __( 'We hate spam too, unsubscribe at any time.', 'wp-chimp' ),

		// Notices message.
		'subscribed_notice' => __( 'You\'ve sucessfully subscribed.', 'wp-chimp' ),
		'error_notice' => __( 'Oops!, an unexpected error occured. Please try it again in a moment.', 'wp-chimp' ),
		'email_invalid_notice' => __( 'It looks like your email address is invalid.', 'wp-chimp' ),
		'double_optin_notice' => __( 'You\'re almost done. Please check your email box to confirm your subscription.', 'wp-chimp' ),

		// translators: %1$s the MailChimp List knowledgebase link URL, %2$s the "Chimp" setting page.
		'inactive_notice' => sprintf( __( 'Subscription Form is currently inactive. You might haven\'t yet input the MailChimp API key to %1$s or your MailChimp account might not contain a %2$s.', 'wp-chimp' ), '<a href="' . admin_url( 'options-general.php?page=wp-chimp' ) . '" target="_blank" class="wp-chimp-notice__url">' . __( 'the Settings page', 'wp-chimp' ) . '</a>', '<a href="https://kb.mailchimp.com/lists" target="_blank" class="wp-chimp-notice__url">' . __( 'List', 'wp-chimp' ) . '</a>' ),
	];

	return isset( $locale[ $key ] ) ? $locale[ $key ] : $locale;
}

/**
 * Echo a translate-able string.
 *
 * This function is meant to echo out a single translate-able string thus the `$key`
 * paramater is required.
 *
 * @since 0.1.0
 *
 * @param string $key (Required) An array key to select the string from `get_locale_strings`.
 * @return void
 */
function the_locale_strings( $key ) {

	if ( empty( $key ) ) {
		return;
	}

	$locale_strings = get_the_locale_strings( $key );
	echo wp_kses( $locale_strings, [
		'a' => [
			'href' => true,
			'target' => true,
			'class' => true,
		],
	] );
}

/**
 * Retrieve the list of attributes as the default values on the "Subscription Form".
 *
 * @since 0.1.0
 *
 * @return array The list of attributes.
 */
function get_the_default_attrs() {

	return [
		'list_id' => get_the_default_list(),
		'title' => get_the_locale_strings( 'title' ),
		'heading_text' => get_the_locale_strings( 'heading_text' ),
		'sub_heading_text' => get_the_locale_strings( 'sub_heading_text' ),
		'email_placeholder_text' => get_the_locale_strings( 'email_placeholder_text' ),
		'button_text' => get_the_locale_strings( 'button_text' ),
		'footer_text' => get_the_locale_strings( 'footer_text' ),
	];
}

/**
 * Undocumented function
 *
 * @since 0.1.0
 *
 * @return string
 */
function get_the_inactive_notice() {
	if ( current_user_can( 'administrator' ) ) :
		ob_start();
	?>
	<div class="wp-chimp-inactive">
		<p class="wp-chimp-inactive__content"><?php the_locale_strings( 'inactive_notice' ); ?></p>
	</div>
	<?php

	$notice = ob_get_contents();
	ob_end_clean();
	endif;

	return $notice;
}

/**
 * Echo the Subscription Form inactive notice.
 *
 * The notification will only appear to the admin.
 *
 * @since 0.1.0
 *
 * @return void
 */
function the_inactive_notice() {
	$notice = get_the_inactive_notice();
	echo wp_kses( $notice, [
		'div' => [
			'class' => true,
		],
		'p' => [
			'class' => true,
		],
		'a' => [
			'href' => true,
			'target' => true,
			'class' => true,
		],
	] );
}

/**
 * Render the "Subscription Form" HTML output.
 *
 * Handles "Subscription Form" output from the Widget, the Gutenberg Block,
 * and the Shortcode.
 *
 * @since 0.1.0
 *
 * @param  array $attrs The attributes to assign to the .
 * @return string The "Subscription Form" HTML markup or a notice if it's inactive.
 */
function render( array $attrs ) {

	if ( ! Includes\is_mailchimp_api_valid() || empty( $attrs['list_id'] ) || 0 >= get_the_lists_count() ) {
		return get_the_inactive_notice();
	}

	$attrs = wp_parse_args( $attrs, get_the_default_attrs() );
	$action_url = Includes\get_the_rest_api_url() . "/lists/{$attrs['list_id']}";

	ob_start();
	?>

	<div class="wp-chimp-subscription-form">
		<h3 class="wp-chimp-subscription-form__heading"><?php echo esc_html( $attrs['heading_text'] ); ?></h3>
		<p class="wp-chimp-subscription-form__sub-heading">
	<?php
		echo wp_kses( $attrs['sub_heading_text'], [
			'strong' => [],
			'em' => [],
			'a' => [
				'href' => true,
				'target' => true,
			],
		] );
	?>
		</p>
		<div class="wp-chimp-notice"></div>
		<form class="wp-chimp-form" method="POST" action="<?php echo esc_attr( $action_url ); ?>">
			<fieldset class="wp-chimp-form__fieldset">
				<input class="wp-chimp-form__email-field" name="email" type="email" placeholder="<?php echo esc_html( $attrs['email_placeholder_text'] ); ?>" required>
				<button class="wp-chimp-form__button" type="submit"><?php echo esc_html( $attrs['button_text'] ); ?></button>
			</fieldset>
		</form>
		<p class="wp-chimp-subscription-form__footer">
	<?php
		echo wp_kses( $attrs['footer_text'], [
			'strong' => [],
			'em' => [],
			'a' => [
				'href' => true,
				'target' => true,
			],
		] );
	?>
		</p>
	</div>

	<?php
	$subscription_form = ob_get_contents();
	ob_end_clean();

	return $subscription_form;
}
