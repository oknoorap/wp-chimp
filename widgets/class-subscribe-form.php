<?php
/**
 * File containing the Class to define the "Subscribe Form" Widget
 *
 * @package WP_Chimp
 * @subpackage WP_Chimp/widgets
 */

namespace WP_Chimp\Widgets;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use WP_Widget;
use WP_Chimp\Includes\Functions;
use WP_Chimp\Includes\Utilities;

/**
 * Class to define the Subscribe Form widget.
 *
 * Define functionality of the widget both in the front-end,
 * and in the back-end.
 *
 * @since  0.1.0
 * @author Thoriq Firdaus <thoriqoe@gmail.com>
 */
final class Subscribe_Form extends WP_Widget {

	/**
	 * Specifies the classname and description, instantiates the widget,
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		$this->locale = Functions\get_subscribe_form_locale();

		$options = [
			'classname' => 'wp-chimp-subscribe-form',
			'description' => $this->locale['description'],
		];
		parent::__construct( 'wp-chimp-subscribe-form', $this->locale['title'], $options );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->locale );

		$request  = new \WP_REST_Request( 'GET', '/wp-chimp/v1/lists' );
		$request->set_query_params(array(
			'context' => 'block'
		));

		$response = rest_do_request( $request );
		$data     = $response->get_data();
		$lists    = Utilities\convert_keys_to_snake_case( $data );

		if ( ! isset( $instance['list_id'] ) || empty( $instance['list_id'] ) ) {
			$instance['list_id'] = $lists[0]['list_id'];
		}
		?>
		<style>
			.wp-chimp-list-select {
				padding: 10px;
				background: #f7f7f7;
				border-radius: 3px;
				display: flex;
				align-items: center;
			}
			.wp-chimp-list-select .dashicons-index-card {
				margin-right: 10px;
			}
		</style>
		<p class="wp-chimp-list-select">
			<span class="dashicons dashicons-index-card"></span>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_id' ) ); ?>">
			<?php foreach ( $lists as $key => $list ) : ?>
				<option value="<?php echo $list['list_id'] ?>"><?php echo $list['name']; ?></option>
			<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'heading_text' ) ); ?>"><?php esc_attr_e( 'Heading Text:', 'wp-chimp' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'heading_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'heading_text' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['heading_text'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sub_heading_text' ) ); ?>"><?php esc_attr_e( 'Sub-heading Text:', 'wp-chimp' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sub_heading_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sub_heading_text' ) ); ?>" rows="2"><?php echo esc_html( $instance['sub_heading_text'] ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'input_email_placeholder' ) ); ?>"><?php esc_attr_e( 'Input Email Placeholder:', 'wp-chimp' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'input_email_placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'input_email_placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['input_email_placeholder'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_attr_e( 'Button Text:', 'wp-chimp' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['button_text'] ); ?>">
		</p>

	<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

	}
}

