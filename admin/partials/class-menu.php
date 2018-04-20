<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wp-chimp.com
 * @since      0.1.0
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/admin/partials
 */

namespace WP_Chimp\Admin\Partials;

/**
 * Class that register new menu in the Admin area and load the page.
 *
 * @since 0.1.0
 */
class Menu {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 0.1.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 * @param Page   $admin_page  The WP_Chimp\Admin\Partials\Page instance to attache to the menu.
	 */
	public function __construct( $plugin_name, $version, Page $admin_page ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->admin_page  = $admin_page;
	}

	/**
	 * Register a new menu page in the Admin.
	 *
	 * @since 0.1.0
	 */
	public function register_menu() {

		$menu_title = __( 'Chimp', 'wp-chimp' );
		$page_title = __( 'Chimp Settings', 'wp-chimp' );

		add_options_page( $page_title, $menu_title, 'manage_options', $this->plugin_name, [ $this->admin_page, 'render_form' ] );
	}

	/**
	 * Function to add a tab to the Contextual Help menu in the setting page.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function add_help_tabs() {

		$screen = get_current_screen();

		if ( 'settings_page_' . $this->plugin_name === $screen->id ) {

			$screen->add_help_tab([
				'id'       => "{$this->plugin_name}-overview",
				'title'    => __( 'Overview', 'wp-chimp' ),
				'callback' => [ __CLASS__, 'html_help_tab_overview' ],
			]);
			$screen->set_help_sidebar( self::html_help_tab_sidebar() );
		}
	}

	/**
	 * Render the content of the "Overview" section in the Help tab in the plugin Admin Page.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	static public function html_help_tab_overview() {
		?>

		<p><?php esc_html_e( 'This screen will show a table of the Lists registered in your MailChimp account.', 'wp-chimp' ); ?></p>

		<p><?php esc_html_e( 'You will need to add the MailChimp API key on the provided input field on this screen. Make sure that it\'s a valid API key and that the status is "Enabled". Once the API key is added and validated, it will retrieve all the MailChimp Lists to along with a number of related data such such as the ID, the name, the number of subscribers, and whether the List is double-optin.', 'wp-chimp' ); ?></p>

		<p><?php esc_html_e( 'In addition, it will also show the WordPress shortcode of each MailChimp list in which you can copy and paste it to display a MailChimp subcription form for the List selected within the post or the page content.', 'wp-chimp' ); ?></p>
	<?php
	}

	/**
	 * Render the content of the sidebar in the Help tab in the plugin Admin Page.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	static public function html_help_tab_sidebar() {

		$content  = '<p><strong>' . esc_html__( 'For more information:', 'wp-chimp' ) . '</strong></p>';
		$content .= '<p><a href="https://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">' . esc_html__( 'About MailChimp API keys', 'wp-chimp' ) . '</a></p>';
		$content .= '<p><a href="https://codex.wordpress.org/Shortcode" target="_blank">' . esc_html__( 'About WordPress Shortcode', 'wp-chimp' ) . '</a></p>';

		return $content;
	}
}