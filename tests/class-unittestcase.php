<?php
/**
 * PHPUnit Tests: UnitTestCase class
 *
 * @package WP_Chimp/Tests
 * @since 0.1.0
 */

namespace WP_Chimp\Tests;

use WP_UnitTestCase;

use WP_Chimp\Core;

use Brain\Monkey;

/**
 * Basic class to run unit test and integration test.
 *
 * @since 0.1.0
 */
class UnitTestCase extends WP_UnitTestCase {

	/**
	 * The WordPress Database abstraction.
	 *
	 * @since 0.1.0
	 * @var WPDB instance
	 */
	protected $wpdb;

	/**
	 * Set up.
	 *
	 * @inheritDoc
	 * @since 0.1.0
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();

		$this->wpdb = $GLOBALS['wpdb'];

		$this->lists_table = new Core\Lists\Table();
		$this->lists_table->maybe_upgrade(); // Install database.
	}

	/**
	 * Check if the database is created.
	 *
	 * @since 0.1.0
	 */
	public function test_check_db_install() {
		$this->assertEquals( "{$this->wpdb->prefix}chimp_lists", $this->wpdb->chimp_lists );
	}

	/**
	 * Tear down.
	 *
	 * @inheritDoc
	 * @since 0.1.0
	 */
	public function tearDown() {
		self::delete_all_data();

		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Delete custom tables created by the plugin.
	 *
	 * @since 0.1.0
	 */
	protected static function delete_all_data() {
		$wpdb = $GLOBALS['wpdb'];
		$wpdb->query( "DELETE FROM $wpdb->chimp_lists" );
	}
}
