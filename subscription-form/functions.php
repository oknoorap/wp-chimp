<?php
/**
 * File to contains the core functions used in the "Subscription Form"
 *
 * @since 0.1.0
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
	$data = $response->get_data();

	return Utilities\convert_keys_to_snake_case( $data );
}

/**
 * Undocumented function
 *
 * @return void
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
 * @return array The list of translateable strings.
 */
function get_the_locale_strings( $key = '' ) {

	$locale = [
		'title' => __( 'Subscription Form', 'wp-chimp' ),
		'description' => __( 'Display a MailChimp subscription form.', 'wp-chimp' ),
		'heading_text' => __( 'Subscribe', 'wp-chimp' ),
		'sub_heading_text' => __( 'Get notified of our next update right to your inbox', 'wp-chimp' ),
		'email_placeholder_text' => __( 'Enter your email address', 'wp-chimp' ),
		'button_text' => __( 'Submit', 'wp-chimp' ),
		'footer_text' => __( 'We hate spam too, unsubscribe at any time.', 'wp-chimp' ),

		// translators: %1$s the MailChimp List knowledgebase link URL, %2$s the "Chimp" setting page.
		'inactive_notice' => sprintf( __( 'Subscription Form is currently inactive. You might haven\'t yet input the MailChimp API key to %1$s or your MailChimp account might not contain a %2$s.', 'wp-chimp' ), '<a href="' . admin_url( 'options-general.php?page=wp-chimp' ) . '" target="_blank" class="wp-chimp-notice__url">' . __( 'the Settings page', 'wp-chimp' ) . '</a>', '<a href="https://kb.mailchimp.com/lists" target="_blank" class="wp-chimp-notice__url">' . __( 'List', 'wp-chimp' ) . '</a>' ),
	];

	return isset( $locale[ $key ] ) ? $locale[ $key ] : $locale;
}

/**
 * Echo the locale strings.
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
 * Retrieve the "Subscription Form" default attributes.
 *
 * @since 0.1.0
 *
 * @return array
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
 * @return void
 */
function get_the_inactive_notice() {

	$notice = '';

	if ( current_user_can( 'administrator' ) ) :
		ob_start();
	?>
	<div class="wp-chimp-notice wp-chimp-notice--warning">
		<p class="wp-chimp-notice__content"><?php the_locale_strings( 'inactive_notice' ); ?></p>
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

	if ( ! Includes\is_mailchimp_api_valid() || empty( $attrs['list_id'] ) || 0 >= get_the_lists_count() ) {
		return get_the_inactive_notice();
	}

	$attrs = wp_parse_args( $attrs, get_the_default_attrs() );
	$action_url = Includes\get_the_rest_api_url() . "lists/{$attrs['list_id']}";

	ob_start();
	?>

	<div class="wp-chimp-subscription-form">
		<h3 class="wp-chimp-subscription-form__heading"><?php echo esc_html( $attrs['heading_text'] ); ?></h3>
		<p class="wp-chimp-subscription-form__sub-heading">
		<?php
			echo wp_kses( $attrs['sub_heading_text'], [
				'strong' => true,
				'em' => true,
				'a' => [
					'href' => true,
					'target' => true,
				],
			] );
		?>
		</p>
		<form class="wp-chimp-subscription-form__inputs" method="POST" action="<?php echo esc_attr( $action_url ); ?>">
			<input class="wp-chimp-subscription-form__email-field" name="email" type="email" placeholder="<?php echo esc_html( $attrs['email_placeholder_text'] ); ?>">
			<button class="wp-chimp-subscription-form__button"><?php echo esc_html( $attrs['button_text'] ); ?></button>
		</form>
		<p class="wp-chimp-subscription-form__footer">
		<?php
			echo wp_kses( $attrs['footer_text'], [
				'strong' => true,
				'em' => true,
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
