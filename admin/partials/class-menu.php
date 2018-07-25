<?php
/**
 * Admin: Menu class
 *
 * @package WP_Chimp\Admin
 * @since 0.1.0
 */

namespace WP_Chimp\Admin\Partials;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Chimp\Core\Plugin_Base;

/**
 * Class to register a new menu in the admin area.
 *
 * @since 0.1.0
 * @since 0.3.0 Extends the Core\Plugin_Base class.
 */
class Menu extends Plugin_Base {

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.3.0
	 */
	public function run() {

		$this->loader->add_action( 'admin_menu', $this, 'register_menu' );
		$this->loader->add_action( 'current_screen', $this, 'register_help_tabs' );
	}

	/**
	 * Register a new menu page in the admin.
	 *
	 * @since 0.1.0
	 */
	public function register_menu() {

		$menu_title = __( 'Chimp', 'wp-chimp' );
		$page_title = __( 'Chimp Settings', 'wp-chimp' );

		add_options_page( $page_title, $menu_title, 'manage_options', $this->plugin_name, [ __NAMESPACE__ . '\\Page', 'render_form' ] );
	}

	/**
	 * Add a tab to the contextual help menu in the admin page.
	 *
	 * @since 0.1.0
	 */
	public function register_help_tabs() {

		$screen = get_current_screen();

		if ( 'settings_page_' . $this->plugin_name === $screen->id ) {

			$screen->add_help_tab(
				[
					'id'       => "{$this->plugin_name}-overview",
					'title'    => __( 'Overview', 'wp-chimp' ),
					'callback' => [ __CLASS__, 'html_help_tab_overview' ],
				]
			);

			$screen->add_help_tab(
				[
					'id'       => "{$this->plugin_name}-mailchimp-api",
					'title'    => __( 'MailChimp API', 'wp-chimp' ),
					'callback' => [ __CLASS__, 'html_help_tab_mailchimp_api' ],
				]
			);
			$screen->set_help_sidebar( self::html_help_tab_sidebar() );
		}
	}

	/**
	 * Render the content of the "Overview" section in the Help tab in the plugin Admin Page.
	 *
	 * @since 0.1.0
	 */
	public static function html_help_tab_overview() {
		?>
		<p><?php esc_html_e( 'MailChimp is one of the most populars, if not the most popular, marketing automation and email marketing platform. This is the setting page where you can connect your site to your MailChimp account and, once it is connected, provides you the ability to display a MailChimp subscription form on the post, page and the Widget area.', 'wp-chimp' ); ?></p>
		<p><?php esc_html_e( 'To get started, you will need a MailChimp API key.', 'wp-chimp' ); ?></p>
		<?php
	}

	/**
	 * Render the content of the "MailChimp API" section in the Help tab in the plugin Admin Page.
	 *
	 * @since 0.2.0
	 */
	public static function html_help_tab_mailchimp_api() {

		// Translators: %s link to MailChimp help article.
		$about_mailchimp_api_key = sprintf( __( 'If you want to connect your site to your MailChimp account, you\'ll first need to generate an API key. This reference from MailChimp, %s, on how to create an API key in your account', 'wp-chimp' ), '<a href="https://mailchimp.com/help/about-api-keys/" target="_blank">About API Keys</a>' );
		?>

		<p>
		<?php
			echo wp_kses( $about_mailchimp_api_key, [
				'a' => [
					'href' => true,
					'target' => true,
				],
			] );
		?>
		</p>

		<p><?php esc_html_e( 'Then add the API key to the provided input field on this page. Make sure that it\'s a valid API key status is "Enabled". Once the it is added and validated, it will start retrieving the MailChimp lists from your account along with a few of their details such as the list ID, the name, the number of subscribers, and display them all together on a table in this page.', 'wp-chimp' ); ?></p>
		<?php
	}

	/**
	 * Render the content of the sidebar in the Help tab in the plugin Admin Page.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public static function html_help_tab_sidebar() {

		$content  = '<p><strong>' . esc_html__( 'For more information:', 'wp-chimp' ) . '</strong></p>';
		$content .= '<p><a href="https://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">' . esc_html__( 'About MailChimp API keys', 'wp-chimp' ) . '</a></p>';
		$content .= '<p><a href="https://codex.wordpress.org/Shortcode" target="_blank">' . esc_html__( 'About WordPress Shortcode', 'wp-chimp' ) . '</a></p>';

		return $content;
	}
}
