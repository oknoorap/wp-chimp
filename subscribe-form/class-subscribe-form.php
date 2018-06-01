<?php

namespace WP_Chimp\Subscribe_Form;

/**
 * Register and render MailChimp Form block.
 *
 * @since 0.1.0
 */
final class Subscribe_Form {

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
			'wp-chimp-subscribe-form-editor',
			plugins_url( $block_js, __FILE__ ),
			[
				'wp-blocks',
				'wp-i18n',
				'wp-element',
			],
			filemtime( "$this->dir/$block_js" )
		);

		$editor_css = 'assets/editor.css';
		wp_register_style(
			'wp-chimp-subscribe-form-editor',
			plugins_url( $editor_css, __FILE__ ),
			[ 'wp-blocks' ],
			filemtime( "$this->dir/$editor_css" )
		);

		$style_css = 'assets/style.css';
		wp_register_style(
			'wp-chimp-subscribe-form',
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

			register_block_type( 'wp-chimp/subscribe-form', [
				'editor_script'   => 'wp-chimp-subscribe-form-editor',
				'editor_style'    => 'wp-chimp-subscribe-form-editor',
				'style'           => 'wp-chimp-subscribe-form',
				'render_callback' => 'WP_Chimp\\Includes\\Functions\\render_subscribe_form' ,
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
}
