<?php
/**
 * File containing the Class to register the "Subscription Form"
 *
 * @package WP_Chimp
 * @subpackage WP_Chimp/widgets
 */

namespace WP_Chimp\Subscription_Form;

use WP_Chimp\Includes\Utilities;

/**
 * Class to register "Subscription Form".
 *
 * The Class will register the components surrounding the "Subscription Form"
 * such as the scripts, styles, widget, shortcode, translateable strings, etc.
 *
 * @since 0.1.0
 */
final class Subscription_Form {

	/**
	 * The directory
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	private $dir;

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->dir = dirname( __FILE__ );
	}

	/**
	 * Function to register the stylesheet and JavaScript file for the
	 * Subscribe Form.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_scripts() {

		$block_js = 'assets/block.js';
		wp_register_script(
			'wp-chimp-subscription-form-editor',
			plugins_url( $block_js, __FILE__ ),
			[
				'wp-blocks',
				'wp-i18n',
				'wp-element',
			],
			filemtime( "$this->dir/$block_js" )
		);

		$script_js = 'assets/script.js';
		wp_register_script(
			'wp-chimp-subscription-form',
			plugins_url( $script_js, __FILE__ ),
			[ 'jquery' ],
			filemtime( "$this->dir/$script_js" )
		);

		$editor_css = 'assets/editor.css';
		wp_register_style(
			'wp-chimp-subscription-form-editor',
			plugins_url( $editor_css, __FILE__ ),
			[ 'wp-blocks' ],
			filemtime( "$this->dir/$editor_css" )
		);

		$style_css = 'assets/style.css';
		wp_register_style(
			'wp-chimp-subscription-form',
			plugins_url( $style_css, __FILE__ ),
			[ 'wp-blocks' ],
			filemtime( "$this->dir/$style_css" )
		);
	}

	/**
	 * Function to register the Subscribe Form block to Gutenberg interface.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_block() {

		if ( function_exists( 'register_block_type' ) ) {

			$locale = get_the_locale_strings();

			register_block_type( 'wp-chimp/subscription-form', [
				'editor_script'   => 'wp-chimp-subscription-form-editor',
				'editor_style'    => 'wp-chimp-subscription-form-editor',
				'script'          => 'wp-chimp-subscription-form',
				'style'           => 'wp-chimp-subscription-form',
				'render_callback' => __NAMESPACE__ . '\\render',
				'attributes'      => [
					'list_id' => [
						'type' => 'string',
						'default' => get_the_default_list(),
					],
					'heading_text' => [
						'type' => 'string',
						'default' => get_the_locale_strings( 'heading_text' ),
					],
					'sub_heading_text' => [
						'type' => 'string',
						'default' => get_the_locale_strings( 'sub_heading_text' ),
					],
					'input_email_placeholder' => [
						'type' => 'string',
						'default' => get_the_locale_strings( 'input_email_placeholder' ),
					],
					'button_text' => [
						'type' => 'string',
						'default' => get_the_locale_strings( 'button_text' ),
					],
				],
			] );
		}
	}

	/**
	 * Function to register the "Subscription Form" widget.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_widget() {
		register_widget( __NAMESPACE__ . '\\Widget' );
	}

	/**
	 * Function to register the "Subscription Form" shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_shortcode() {
		add_shortcode( 'wp-chimp', [ __NAMESPACE__ . '\\Shortcode', 'render' ] );
	}

	/**
	 * Function to register translateable string displayed in the Subscription Form.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_locale_strings() {

		$locale = get_the_locale_strings();

		wp_localize_script( 'wp-chimp-subscription-form-editor', 'wpChimpL10n', Utilities\convert_keys_to_camel_case( $locale ) );
	}
}
