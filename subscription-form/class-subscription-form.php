<?php
/**
 * Subscription Form: Main class
 *
 * @package WP_Chimp/Subscription_Form
 * @since 0.1.0
 */

namespace WP_Chimp\Subscription_Form;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Chimp\Core;

/**
 * Main class to register the "Subscription Form".
 *
 * Register components surrounding the "Subscription Form" such as scripts, styles,
 * widget, shortcode, locale strings, etc.
 *
 * @since 0.1.0
 * @author Thoriq Firdaus <thoriqoe@gmail.com>
 */
final class Subscription_Form {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 0.1.0
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 0.1.0
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The filename of plugin.
	 *
	 * This might be used for WordPress functions requiring the path to
	 * the main plugin file, such as `plugin_dir_path()` and `plugin_basename()`.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $file_path;

	/**
	 * The plugin directory
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $dir_path;

	/**
	 * Undocumented function
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @param string $file_path   The plugin file path.
	 */
	public function __construct( $plugin_name, $version, $file_path ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->file_path = $file_path;
		$this->dir_path = dirname( $file_path );
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

		$block_js = 'assets/js/subscription-form-editor.js';
		wp_register_script(
			'wp-chimp-subscription-form-editor',
			plugins_url( $block_js, $this->file_path ),
			[
				'wp-blocks',
				'wp-i18n',
				'wp-element',
			],
			filemtime( "{$this->dir_path}/{$block_js}" )
		);

		$script_js = 'assets/js/subscription-form.js';
		wp_register_script(
			'wp-chimp-subscription-form',
			plugins_url( $script_js, $this->file_path ),
			[ 'jquery' ],
			filemtime( "{$this->dir_path}/{$script_js}" )
		);

		$editor_css = 'assets/css/subscription-form-editor.css';
		wp_register_style(
			'wp-chimp-subscription-form-editor',
			plugins_url( $editor_css, $this->file_path ),
			[ 'wp-blocks' ],
			filemtime( "{$this->dir_path}/{$editor_css}" )
		);

		$style_css = 'assets/css/subscription-form.css';
		wp_register_style(
			'wp-chimp-subscription-form',
			plugins_url( $style_css, $this->file_path ),
			[ 'wp-blocks' ],
			filemtime( "{$this->dir_path}/{$style_css}" )
		);
	}

	/**
	 * Function to register the Subscribe Form block to Gutenberg interface.
	 *
	 * @since 0.1.0
	 *
	 * @return void|null Returns `null` if the Gutenberg block is not present.
	 */
	public function register_block() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type( 'wp-chimp/subscription-form', [
			'editor_script' => 'wp-chimp-subscription-form-editor',
			'editor_style' => 'wp-chimp-subscription-form-editor',
			'style' => 'wp-chimp-subscription-form',
			'render_callback' => __NAMESPACE__ . '\\render',
			'attributes' => [
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
				'email_placeholder_text' => [
					'type' => 'string',
					'default' => get_the_locale_strings( 'email_placeholder_text' ),
				],
				'button_text' => [
					'type' => 'string',
					'default' => get_the_locale_strings( 'button_text' ),
				],
				'footer_text' => [
					'type' => 'string',
					'default' => get_the_locale_strings( 'footer_text' ),
				],
			],
		] );
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
	public function admin_enqueue_locale_scripts() {

		$locale = get_the_locale_strings();
		$data = Core\convert_keys_to_camel_case( $locale );

		wp_localize_script( 'wp-chimp-subscription-form-editor', 'wpChimpL10n', $data );
	}

	/**
	 * Undocumented function
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_locale_scripts() {

		$locale = [
			'subscribed_notice' => get_the_locale_strings( 'subscribed_notice' ),
			'email_invalid_notice' => get_the_locale_strings( 'email_invalid_notice' ),
			'error_notice' => get_the_locale_strings( 'error_notice' ),
			'double_optin_notice' => get_the_locale_strings( 'double_optin_notice' ),
		];
		$data = Core\convert_keys_to_camel_case( $locale );

		wp_localize_script( 'wp-chimp-subscription-form', 'wpChimpL10n', $data );
	}

	/**
	 * Undocumented function
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'wp-chimp-subscription-form' );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 0.1.0
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the plugin file path.
	 *
	 * @since 0.1.0
	 *
	 * @return string The plugin file path.
	 */
	public function get_file_path() {
		return $this->file_path;
	}

	/**
	 * Retrieve the plugin directory path.
	 *
	 * @since 0.1.0
	 *
	 * @return string The plugin directory path.
	 */
	public function get_dir_path() {
		return $this->dir_path;
	}
}
