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

namespace WP_Chimp;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Class that register new menu in the Admin area and load the page.
 *
 * @since 0.1.0
 */
class Admin_Page {

	/**
	 * The ID of this plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    string  $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    string  $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin settings.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    array   $settings The list value of the plugin settings.
	 */
	private $settings;

	/**
	 * The MailChimp API key.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var    string  $mailchimp_api_key The value of user.
	 */
	private $mailchimp_api_key;

	/**
	 * The class constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->mailchimp_api_key = get_option( 'wp_chimp_mailchimp_api_key', '' );
	}

	/**
	 * Register the page settings
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_page() {

		register_setting( $this->plugin_name, 'wp_chimp_mailchimp_api_key', [
			'type'              => 'string',
			'description'       => 'The MailChimp API key associated with WP-Chimp plugin',
			'sanitize_callback' => 'sanitize_text_field',
		] );

		add_settings_section( 'section-mailchimp', '', [ $this, 'html_section_settings' ], $this->plugin_name );
		add_settings_field( 'mailchimp-api-key', __( 'API Key', 'wp-chimp' ), [ $this, 'html_field_mailchimp_api_key' ], $this->plugin_name, 'section-mailchimp', [
			'label_for' => 'field-mailchimp-api-key',
		] );
	}

	/**
	 * Function to get the current state to pass in the JavaScript
	 *
	 * @since 0.1.0
	 *
	 * @return string|false The JSON encoded string, or false if it cannot be encoded.
	 */
	public function get_state() {

		// TODO: change the init value.
		$state = [
			'apiKey' => (bool) $this->mailchimp_api_key,
			'init'   => false,
		];

		return wp_json_encode( $state, 0, 3 );
	}

	/**
	 * Render the setting form on the page
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function render_form() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		} ?>

		<div class="wrap wp-chimp-wrap" id="wp-chimp-settings" data-state='<?php echo esc_attr( $this->get_state() ); ?>'>
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php if ( $this->mailchimp_api_key ) : ?>
			<table class="widefat striped wp-chimp-table" id="wp-chimp-mailchimp-list">
				<thead>
					<tr>
						<th scope="col" class="wp-chimp-table__th-id"><?php esc_html_e( 'ID', 'wp-chimp' ); ?></th>
						<th scope="col" class="wp-chimp-table__th-name"><?php esc_html_e( 'Name', 'wp-chimp' ); ?></th>
						<th scope="col" class="wp-chimp-table__th-subscribers"><?php esc_html_e( 'Subscribers', 'wp-chimp' ); ?></th>
						<th scope="col" class="wp-chimp-table__th-last-sync"><?php esc_html_e( 'Last Sync.', 'wp-chimp' ); ?></th>
						<th scope="col" class="wp-chimp-table__th-shortcode"><?php esc_html_e( 'Shortcode', 'wp-chimp' ); ?></th>
					</tr>
				</thead>
				<tbody id="wp-chimp-mailchimp-list-data">
					<tr class="no-items">
						<td colspan="5">No MailChimp list found.</td>
					</tr>
				</tbody>
			</table>
			<?php endif; ?>

			<h2 class="nav-tab-wrapper">
				<span class="nav-tab nav-tab-active"><?php echo esc_html( 'MailChimp' ); ?></span>
			</h2>
			<form action="options.php" method="post">
			<?php
				settings_fields( 'wp-chimp' );
				do_settings_sections( 'wp-chimp' );
				submit_button( __( 'Save Settings', 'wp-chimp' ) );
			?>
			</form>
		</div>
	<?php
	}

	/**
	 * Function that fills the section with the desired content.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function html_section_settings() {}

	/**
	 * Render the "MailChimp API Key" input field
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function html_field_mailchimp_api_key() {
	?>
		<input type="text" name="wp_chimp_mailchimp_api_key" id="field-mailchimp-api-key" class="regular-text" value="<?php echo esc_attr( $this->mailchimp_api_key ); ?>" />
		<p class="description"><?php esc_html_e( 'Add your MailChimp API key' ); ?>. <a href="https://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank"><?php esc_html_e( 'How to generate the API key?', 'wp-chimp' ); ?></a></p>
	<?php
	}

	/**
	 * Add the action link in Plugin list screen.
	 *
	 * @since 0.1.0
	 * @access public
	 *
	 * @param  array $links WordPress built-in links (e.g. Activate, Deactivate, and Edit).
	 * @return array        Action links with the new one added.
	 */
	public function register_action_links( $links ) {

		$markup   = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=%2$s' ) ) . '">%1$s</a>';
		$settings = [
			'settings' => sprintf( $markup, __( 'Settings', 'wp-chimp' ), $this->plugin_name ),
		];

		return array_merge( $settings, $links );
	}
}
