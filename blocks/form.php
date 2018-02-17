<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package WP_Chimp
 * @subpackage WP_Chimp/blocks
 */

namespace WP_Chimp;

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
 */
function form_block_init() {
	$dir = dirname( __FILE__ );

	$block_js = 'form/block.js';
	wp_register_script(
		'form-block-editor',
		plugins_url( $block_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$block_js" )
	);

	$editor_css = 'form/editor.css';
	wp_register_style(
		'form-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(
			'wp-blocks',
		),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'form/style.css';
	wp_register_style(
		'form-block',
		plugins_url( $style_css, __FILE__ ),
		array(
			'wp-blocks',
		),
		filemtime( "$dir/$style_css" )
	);

	register_block_type( 'wp-chimp/form', array(
		'editor_script' => 'form-block-editor',
		'editor_style' => 'form-block-editor',
		'style' => 'form-block'
	) );
}
add_action( 'init', __NAMESPACE__ . '\\form_block_init' );
