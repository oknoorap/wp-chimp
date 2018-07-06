<?php
/**
 * MailChimp List Table: DB_MailChimp_Lists class
 *
 * @package WP_Chimp/Core
 * @since 0.1.0
 */

namespace WP_Chimp\Core\Lists;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

use WP_Chimp\Core\Database;

/**
 * Setup the "chimp_list" database schema
 *
 * @since 0.1.0
 *
 * @property string $name
 * @property string $version
 */
final class Table extends Database {

	/**
	 * Table name
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $name = 'chimp_lists';

	/**
	 * Database version
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $version = 201803220001;

	/**
	 * Setup the database schema
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
	 * Handle schema changes
	 *
	 * @since 0.1.0
	 */
	protected function upgrade() {}
}
