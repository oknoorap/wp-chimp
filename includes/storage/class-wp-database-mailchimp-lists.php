<?php
/**
 * MailChimp List Table: DB_MailChimp_Lists class
 *
 * @link       https://wp-chimp.com
 * @since      0.1.0
 *
 * @package    WP_Chimp
 * @subpackage WP_Chimp/database
 */

namespace WP_Chimp\Storage;

/**
 * Setup the "chimp_mailchimp_list" database schema
 *
 * @since 0.1.0
 */
final class WP_Database_MailChimp_Lists extends WP_Database {

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $name = 'chimp_mailchimp_lists';

	/**
	 * Database version
	 *
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
