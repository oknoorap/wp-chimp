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

namespace WP_Chimp\Storage;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use \WP_Background_Process;

/**
 * Class that register new menu in the Admin area and load the page.
 *
 * @since 0.1.0
 */
final class MailChimp_Lists_Process extends WP_Background_Process {

	/**
	 * Function to assign the MailChimp list
	 *
	 * @return void
	 */
	public function assign_query( WP_Chimp\Storage\MailChimp_Lists_Query $lists_query ) {
		$this->lists_query = $lists_query;
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 * @return mixed
	 */
	protected function task( $item ) {
		$this->lists_query->insert();
		return false; // Actions to perform.
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
	}
}
