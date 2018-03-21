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

/**
 * Class that register new menu in the Admin area and load the page.
 *
 * @since 0.1.0
 */
class Admin_Page {

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

		$this->settings = get_option( 'wp_chimp_settings' );
	}

	/**
	 * Register the page settings
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_page() {

		register_setting( $this->plugin_name, 'wp_chimp_settings', [
			'sanitize_callback' => [ $this, 'sanitize_settings' ],
		] );

		add_settings_section( 'section-settings', '', [ $this, 'html_section_settings' ], $this->plugin_name );

		add_settings_field( 'mailchimp-api-key', __( 'API Key', 'wp-chimp' ), [ $this, 'html_field_mailchimp_api_key' ], $this->plugin_name, 'section-settings', [
			'label_for' => 'field-mailchimp-api-key',
		] );
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

		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<h2 class="nav-tab-wrapper">
				<span class="nav-tab nav-tab-active"><?php esc_html_e( 'Settings', 'wp-chimp' ); ?></span>
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

		$value = isset( $this->settings['mailchimp_api_key'] ) ? $this->settings['mailchimp_api_key'] : '';
	?>
		<input type="text" name="wp_chimp_settings[mailchimp_api_key]" id="field-mailchimp-api-key" class="regular-text" value="<?php echo esc_attr( $value ); ?>" />
		<p class="description"><?php esc_html_e( 'Add your MailChimp API key.' ); ?><a href="https://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank"><?php esc_html_e( 'How to generate the API key?', 'wp-chimp' ); ?></a></p>
	<?php
	}

	/**
	 * Method to sanitize the settings input
	 *
	 * @since 0.1.0
	 *
	 * @param  array $inputs Unsanitized input.
	 * @return array         Sanitized input
	 */
	public function sanitize_settings( array $inputs ) {

		$inputs['mailchimp_api_key'] = sanitize_text_field( $inputs['mailchimp_api_key'] );

		return $inputs;
	}
}
