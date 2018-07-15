<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package WP_Chimp/Core
 * @since 0.1.0
 */

namespace WP_Chimp\Core\Lists;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * Loaded dependencies with Mozart.
 *
 * The prefix looks terrible at best, but no other choice at least
 * for the moment.
 *
 * @since 0.2.0
 * @see https://github.com/coenjacobs/mozart
 */
use WP_Chimp_Packages_WP_Background_Process as WP_Chimp_Background_Process;

/**
 * Class that register new menu in the Admin area and load the page.
 *
 * @since 0.1.0
 */
final class Process extends WP_Chimp_Background_Process {

	/**
	 * The unique wp_cron action.
	 *
	 * @var string
	 */
	protected $action = 'chimp_lists_process';

	/**
	 * Function to assign the MailChimp list.
	 *
	 * @param Query $lists_query The Query class instance.
	 */
	public function set_lists_query( Query $lists_query ) {
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
	 * @since 0.1.0
	 *
	 * @param mixed $data Queue item to iterate over.
	 * @return mixed
	 */
	protected function task( $data ) {

		$list = $this->lists_query->get_by_the_id( $data['list_id'] );
		$data['synced_at'] = date( 'Y-m-d H:i:s' );

		if ( ! empty( $list ) && isset( $list['list_id'] ) && $list['list_id'] === $data['list_id'] ) {
			$this->lists_query->update( $data['list_id'], (array) $data );
		} else {
			$this->lists_query->insert( (array) $data );
		}

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 *
	 * @since 0.1.0
	 */
	protected function complete() {
		parent::complete();

		update_option( 'wp_chimp_lists_init', 1 );
	}
}
