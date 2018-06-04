<?php

namespace WP_Chimp\Subscription_Form;

/**
 * Class to register and render MailChimp Form block.
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

			$locale       = get_locale_strings();
			$default_list = get_default_list();

			register_block_type( 'wp-chimp/subscription-form', [
				'editor_script'   => 'wp-chimp-subscription-form-editor',
				'editor_style'    => 'wp-chimp-subscription-form-editor',
				'script'          => 'wp-chimp-subscription-form',
				'style'           => 'wp-chimp-subscription-form',
				'render_callback' => __NAMESPACE__ . '\\render',
				'attributes'      => [
					'list_id' => [
						'type' => 'string',
						'default' => $default_list,
					],
					'heading_text' => [
						'type'    => 'string',
						'default' => $locale['heading_text'],
					],
					'sub_heading_text' => [
						'type'    => 'string',
						'default' => $locale['sub_heading_text'],
					],
					'input_email_placeholder' => [
						'type'    => 'string',
						'default' => $locale['input_email_placeholder'],
					],
					'button_text' => [
						'type'    => 'string',
						'default' => $locale['button_text'],
					],
				],
			] );
		}
	}

	/**
	 * Function to register the Subscribe Form widget.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_widget() {
		register_widget( __NAMESPACE__ . '\\Widget' );
	}

	/**
	 * Function to register the Subscribe Form shortcode.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_shortcode() {

	}

	/**
	 * Function to register translateable string displayed in the Subscription Form.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_locale_strings() {
		wp_localize_script( 'wp-chimp-subscription-form-editor', 'wpChimpL10n', get_locale_strings() );
	}
}
