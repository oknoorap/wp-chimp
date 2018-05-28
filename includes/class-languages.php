<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wp-chimp.com
 * @since      0.1.0
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/includes
 */

namespace WP_Chimp\Includes;

use WP_Chimp\Includes\Functions;
use WP_Chimp\Includes\Utilities;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    WP_Chimp
 * @subpackage WP_Chimp/includes
 * @author     Thoriq Firdaus <thoriqoe@gmail.com>
 */
class Languages {

	/**
	 * Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @since 0.1.0
	 */
	const DOMAIN = 'wp-chimp';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		__( 'Subscribe to our newsletter', 'wp-chimp' );

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 0.1.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( self::DOMAIN, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Load the scripts related to i18n of the plugin.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * The translatable strings for the Subscribe Form.
		 *
		 * @var array
		 */
		$subscribe_form_locale = [
			'subscribe_form' => Functions\get_subscribe_form_locale()
		];

		/**
		 * The translatable strings with the keys converted to camelCase
		 *
		 * @var array
		 */
		$locale = Utilities\convert_keys_to_camel_case( $subscribe_form_locale );

		wp_localize_script( $this->plugin_name, 'wpChimpL10n', $locale );
		wp_localize_script( 'wp-chimp-subscribe-form-editor', 'wpChimpL10n', $locale );
	}

	/**
	 * Returns Jed-formatted localization data.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	static private function get_jed_locale_data() {

		$translations = get_translations_for_domain( self::DOMAIN );
		$locale = [
			'' => [
				'domain' => self::DOMAIN,
				'lang'   => is_admin() ? get_user_locale() : get_locale(),
			],
		];

		if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
			$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach ( $translations->entries as $msgid => $entry ) {
			$locale[ $msgid ] = $entry->translations;
		}

		return $locale;
	}
}
