<?php
/**
 * Class Test_MailChimp_Lists_Query
 *
 * @package WP_Chimp\Tests;
 */

namespace WP_Chimp;

// Load WP_UnitTestCase.
use \WP_UnitTestCase;

/**
 * The class to test the "Utilities" functions.
 *
 * @since 1.2.3
 */
class Test_MailChimp_Lists_Query extends WP_UnitTestCase {

	/**
	 * The MailChimp_Lists_Query instance
	 *
	 * @var WP_Chimp\Storage\MailChimp_Lists_Query
	 */
	private $lists_query;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		$this->lists_db = new Storage\WP_Database_MailChimp_Lists();
		$this->lists_db->maybe_upgrade();

		$this->lists_query = new Storage\MailChimp_Lists_Query();

		$this->wpdb = $GLOBALS['wpdb'];
	}

	/**
	 * Test the update method.
	 *
	 * @since  0.1.0
	 *
	 * @return void
	 */
	public function test_check_db_install() {
		$this->assertEquals( "{$this->wpdb->prefix}chimp_mailchimp_lists", $this->wpdb->chimp_mailchimp_lists );
	}

	/**
	 * Test method to insert a new entry to the database.
	 *
	 * @see Storage\MailChimp_Lists_Query()->insert();
	 *
	 * @return void
	 */
	public function test_query() {

		$sample_data = [
			[
				'list_id'       => '520524cb3b',
				'name'          => 'MailChimp List 1',
				'subscribers'   => 100,
				'double_opt_in' => 0,
				'synced_at'     => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'       => '320424cb3b',
				'name'          => 'MailChimp List 2',
				'subscribers'   => 200,
				'double_opt_in' => 0,
				'synced_at'     => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'       => '610424aa1c',
				'name'          => 'MailChimp List 3',
				'subscribers'   => 200,
				'double_opt_in' => 0,
				'synced_at'     => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'       => '', // Bad Example of empty ID.
				'name'          => 'MailChimp List 4',
				'subscribers'   => 400,
				'double_opt_in' => 0,
				'synced_at'     => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'       => '729404aa1c',
				'name'          => '', // Bad Example of empty name.
				'subscribers'   => 500,
				'double_opt_in' => 0,
				'synced_at'     => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'       => '827304a9a2',
				'name'          => 'MailChimp List 6',
				'subscribers'   => 600,
				'double_opt_in' => 0,
				'synced_at'     => date( 'Y-m-d H:i:s' ),
				'rand'          => 'Hello World', // Bad Example of empty name.
			],
			[], // Bad example of empty array.
		];

		foreach ( $sample_data as $key => $data ) {
			$this->lists_query->insert( $data );
		}

		$retrieved_data = $this->lists_query->query();

		/**
		 * Assuming that the last 2 of the data are invalid.
		 *
		 * The data with the list ID of '827304a9a2a' would still be
		 * inserted to the table since the 'rand' column will be
		 * filtered-out before inserting the data to the table.
		 */
		$this->assertEquals( 4, count( $retrieved_data ) );

		$retrieved_data_sorted = [];
		foreach ( $retrieved_data as $data ) {
			$retrieved_data_sorted[ $data['list_id'] ] = $data['name'];
		}

		$this->assertTrue( array_key_exists( '520524cb3b', $retrieved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 1', $retrieved_data_sorted['520524cb3b'] );

		$this->assertTrue( array_key_exists( '320424cb3b', $retrieved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 2', $retrieved_data_sorted['320424cb3b'] );

		$this->assertTrue( array_key_exists( '610424aa1c', $retrieved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 3', $retrieved_data_sorted['610424aa1c'] );

		// Ommitted because the 'name' is empty.
		$this->assertFalse( array_key_exists( '729404aa1c', $retrieved_data_sorted ) );

		$this->assertTrue( array_key_exists( '827304a9a2', $retrieved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 6', $retrieved_data_sorted['827304a9a2'] );
	}
}
