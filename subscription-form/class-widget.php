<?php
/**
 * File containing the Class to define the "Subscribe Form" Widget
 *
 * @since 0.1.0
 * @package WP_Chimp
 * @subpackage WP_Chimp/Subscription_Form
 */

namespace WP_Chimp\Subscription_Form;

/* If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Widget;
use WP_REST_Request;
use WP_Chimp\Includes\Utilities;

/**
 * Class to define the "Subscribe Form" widget.
 *
 * Define functionality of the widget both in the front-end,
 * and in the back-end.
 *
 * @since 0.1.0
 * @author Thoriq Firdaus <thoriqoe@gmail.com>
 */
final class Widget extends WP_Widget {

	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	private $locale;

	/**
	 * Lists
	 *
	 * @since 0.1.0
	 * @var array
	 */
	private $lists;

	/**
	 * List of "Subscription Form" default attributes.
	 *
	 * @since 0.1.0
	 * @var array
	 */
	private $default_attrs;

	/**
	 * Specifies the classname and description, instantiates the widget,
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		$this->lists = get_the_lists();
		$this->locale = get_the_locale_strings();
		$this->default_attrs = get_the_default_attrs();

		parent::__construct( 'wp-chimp-subscription-form', $this->locale['title'], [
			'classname'   => 'wp-chimp-subscription-form-widget',
			'description' => $this->locale['description'],
		] );
	}

	/**
	 * Echoes the widget content.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		self::enqueue_scripts();

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		echo $args['before_title'] . esc_html( $title ) . $args['after_title'];

		if ( empty( $this->lists ) ) : ?>
			<div>
			<?php
				echo wp_kses( $this->locale->inactive_notice, [
					'a' => [
						'href' => true,
						'target' => true,
					],
				] );
				?>
			</div>
		<?php
			else :
				echo render( $instance );
			endif;

			echo $args['after_widget'];
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @since 0.1.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$options = wp_parse_args( $instance, $this->default_attrs );

		if ( empty( $this->lists ) ) :
		?>

		<div class="wp-chimp-notice">
			<p><?php echo wp_kses_post( $this->locale->inactive_notice ); ?></p>
		</div>

		<?php else : ?>

		<p class="wp-chimp-list-select">
			<label for="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>">
				<span class="dashicons dashicons-index-card"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'List_ID', 'wp-chimp' ); ?></span>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_id' ) ); ?>">

			<?php
			foreach ( $this->lists as $key => $list ) :
				$selected = $options['list_id'];
				$current  = $list['list_id'];
			?>
				<option value="<?php echo esc_attr( $list['list_id'] ); ?>" <?php selected( $selected, $current, true ); ?>><?php echo esc_html( $list['name'] ); ?></option>
			<?php endforeach; ?>

			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'heading_text' ) ); ?>"><?php esc_attr_e( 'Heading Text:', 'wp-chimp' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'heading_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'heading_text' ) ); ?>" type="text" value="<?php echo esc_attr( $options['heading_text'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sub_heading_text' ) ); ?>"><?php esc_attr_e( 'Sub-heading Text:', 'wp-chimp' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sub_heading_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sub_heading_text' ) ); ?>" rows="2"><?php echo esc_html( $options['sub_heading_text'] ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'input_email_placeholder' ) ); ?>"><?php esc_attr_e( 'Input Email Placeholder:', 'wp-chimp' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'input_email_placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'input_email_placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $options['input_email_placeholder'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_attr_e( 'Button Text:', 'wp-chimp' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $options['button_text'] ); ?>">
		</p>

	<?php
		endif;
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @since 0.1.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via `WP_Widget::form()`.
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		return wp_parse_args( $new_instance, $this->default_attrs );
	}

	/**
	 * Function to load the styles and scripts for the widget.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private static function enqueue_scripts() {

		if ( ! wp_style_is( 'wp-chimp-subscription-form', 'enqueued' ) ) {
			wp_enqueue_style( 'wp-chimp-subscription-form' );
		}

		if ( ! wp_script_is( 'wp-chimp-subscription-form', 'enqueued' ) ) {
			wp_enqueue_script( 'wp-chimp-subscription-form' );
		}
	}
}

