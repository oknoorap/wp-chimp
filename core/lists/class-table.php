<?php
/**
 * Lists: Table class
 *
 * @package WP_Chimp\Core\Lists
 * @since 0.1.0
 */

namespace WP_Chimp\Core\Lists;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Chimp\Core\Loader;
use WP_Chimp\Core\Database;

/**
 * Class to register the custom, `chimp_lists`, table to store the MailChimp lists in the database.
 *
 * @since 0.1.0
 * @since 0.3.0 Adds the `$loader` property, and `set_loder` and `run` method.
 *
 * @property WP_Chimp\Core\Loader $loader
 * @property string $name
 * @property string $version
 */
final class Table extends Database {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 0.3.0
	 * @var WP_Chimp\Core\Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Table name
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $name = 'chimp_lists';

	/**
	 * Database version.
	 *
	 * @since 0.1.0
	 * @var int
	 */
	protected $version = 201803220001;

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 0.3.0
	 */
	public function run() {

		$this->loader->add_action( 'switch_blog', $this, 'switch_blog' );
		$this->loader->add_action( 'admin_init', $this, 'maybe_upgrade' );
	}

	/**
	 * Set the loader to orchestrate WordPress Hooks
	 *
	 * @since 0.3.0
	 *
	 * @param WP_Chimp\Core\Loader $loader The Loader instance.
	 */
	public function set_loader( Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Setup the database schema.
	 *
	 * @since 0.1.0
	 */
	protected function set_schema() {

		$this->schema = "
			id bigint(20) NOT NULL AUTO_INCREMENT,
			list_id varchar(20) NOT NULL DEFAULT '',
			name varchar(200) NOT NULL DEFAULT '',
			subscribers bigint(20) NOT NULL DEFAULT '0',
			double_optin tinyint(1) NOT NULL DEFAULT '0',
			synced_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY list_id (list_id)
		";
	}

	/**
	 * Handle schema changes.
	 *
	 * @since 0.1.0
	 */
	protected function upgrade() {}
}
