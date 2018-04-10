<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package WP_Chimp
 * @subpackage WP_Chimp/blocks
 */

namespace WP_Chimp\Blocks;

/**
 * Register and render MailChimp Form block.
 *
 * @since 0.1.0
 */
final class Subscribe_Form {

	/**
	 * The directory
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
	 * Registers all block assets so that they can be enqueued through Gutenberg in
	 * the corresponding context.
	 *
	 * {@link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts}
	 */
	public function form_block_init() {

		$block_js = 'subscribe-form/block.js';
		wp_register_script(
			'wp-chimp-subscribe-form-block-editor',
			plugins_url( $block_js, __FILE__ ),
			[
				'wp-blocks',
				'wp-i18n',
				'wp-element',
			],
			filemtime( "$this->dir/$block_js" )
		);

		$editor_css = 'subscribe-form/editor.css';
		wp_register_style(
			'wp-chimp-subscribe-form-block-editor',
			plugins_url( $editor_css, __FILE__ ),
			[ 'wp-blocks' ],
			filemtime( "$this->dir/$editor_css" )
		);

		$style_css = 'subscribe-form/style.css';
		wp_register_style(
			'wp-chimp-subscribe-form-block',
			plugins_url( $style_css, __FILE__ ),
			[ 'wp-blocks' ],
			filemtime( "$this->dir/$style_css" )
		);

		register_block_type( 'wp-chimp/form', [
			'editor_script'   => 'wp-chimp-subscribe-form-block-editor',
			'editor_style'    => 'wp-chimp-subscribe-form-block-editor',
			'style'           => 'wp-chimp-subscribe-form-block',
			'render_callback' => [ $this, 'render_form' ],
		] );
	}

	/**
	 * Render the MailCHimp Form block on the front-end
	 *
	 * @param  array $attributes The MailChimp form attributes.
	 * @return string            The MailChimp from HTML markup.
	 */
	public function render_form( $attributes ) {

		$return = '';
		if ( isset( $attributes['mailingList'] ) && ! empty( $attributes['mailingList'] ) ) {
			$return = "<p>{$attributes['mailingList']}</p>";
		}

		return apply_filters( 'wp_chimp_render_block_subscribe_form', $return, $attributes );
	}
}
