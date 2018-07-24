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
use WP_Chimp\Core\Plugin_Base;

/**
 * Main class to register the "Subscription Form".
 *
 * Register components surrounding the "Subscription Form" such as scripts, styles,
 * widget, shortcode, locale strings, etc.
 *
 * @since 0.1.0
 * @since 0.3.0 Extends the Core\Plugin_Base class.
 *
 * @property string $dir_path
 */
final class Subscription_Form extends Plugin_Base {

	/**
	 * The plugin directory path.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $dir_path;

	/**
	 * Constructor.
	 *
	 * Initialize the Class properties.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @param string $file_path   The plugin file path.
	 */
	public function __construct( $plugin_name, $version, $file_path ) {
		parent::__construct( $plugin_name, $version, $file_path );

		$this->dir_path = dirname( $file_path );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.3.0
	 */
	public function run() {

		$this->loader->add_action( 'init', $this, 'register_scripts' );
		$this->loader->add_action( 'init', $this, 'register_block' );
		$this->loader->add_action( 'init', $this, 'register_shortcode' );
		$this->loader->add_action( 'widgets_init', $this, 'register_widget' );

		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts', 30 );
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_locale_scripts', 30 );
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'admin_enqueue_locale_scripts', 30 );
	}

	/**
	 * Register the stylesheet and JavaScript loaded on the Subscrition Form.
	 *
	 * @since 0.1.0
	 */
	public function register_scripts() {

		$block_js = 'assets/js/subscription-form-editor.min.js';
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

		$script_js = 'assets/js/subscription-form.min.js';
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
	 * Register a custom Gutenberg block of the Subscription Form.
	 *
	 * @since 0.1.0
	 */
	public function register_block() {

		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'wp-chimp/subscription-form', [
				'editor_script' => 'wp-chimp-subscription-form-editor',
				'editor_style' => 'wp-chimp-subscription-form-editor',
				'style' => 'wp-chimp-subscription-form',
				'render_callback' => __NAMESPACE__ . '\\render',
				'attributes' => [
					'list_id' => [
						'type' => 'string',
						'default' => Core\get_the_default_list(),
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
			]
		);
	}

	/**
	 * Register the Subscription Form widget.
	 *
	 * @since 0.1.0
	 */
	public function register_widget() {
		register_widget( __NAMESPACE__ . '\\Widget' );
	}

	/**
	 * Register the Subscription Form shortcode.
	 *
	 * @since 0.1.0
	 */
	public function register_shortcode() {
		add_shortcode( 'wp-chimp', [ __NAMESPACE__ . '\\Shortcode', 'render' ] );
	}

	/**
	 * Register translate-able strings loaded in the admin area.
	 *
	 * @since 0.1.0
	 */
	public function admin_enqueue_locale_scripts() {

		$locale = get_the_locale_strings();
		$data = Core\convert_keys_to_camel_case( $locale );

		wp_localize_script( 'wp-chimp-subscription-form-editor', 'wpChimpL10n', $data );
	}

	/**
	 * Register translate-able strings loaded in the front-end.
	 *
	 * @since 0.1.0
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
	 * Load scripts and styles.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'wp-chimp-subscription-form' );
	}
}
