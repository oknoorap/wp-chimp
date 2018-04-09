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
	 * The WordPress Database abstraction
	 *
	 * @var wpbd instance
	 */
	private $wpdb;

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
		$this->wpdb        = $GLOBALS['wpdb'];
		$this->sample_data = [
			[
				'list_id'      => '520524cb3b',
				'name'         => 'MailChimp List 1',
				'subscribers'  => 100,
				'double_optin' => 0,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'      => '320424cb3b',
				'name'         => 'MailChimp List 2',
				'subscribers'  => 200,
				'double_optin' => 0,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'      => '610424aa1c',
				'name'         => 'MailChimp List 3',
				'subscribers'  => 200,
				'double_optin' => 0,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'      => '',                      // Bad Example of empty ID.
				'name'         => 'MailChimp List 4',
				'subscribers'  => 400,
				'double_optin' => 0,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'      => '729404aa1c',
				'name'         => '',                      // Bad Example of empty name.
				'subscribers'  => 500,
				'double_optin' => 0,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
			],
			[
				'list_id'      => '827304a9a2',
				'name'         => 'MailChimp List 6',
				'subscribers'  => 600,
				'double_optin' => 0,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
				'rand'         => 'Hello World',           // Bad Example of empty name.
			],
			[], // Bad example of empty array.
		];

		// Insert the data to the table.
		foreach ( $this->sample_data as $data ) {
			$this->lists_query->insert( $data );
		}
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
	 * @since  0.1.0
	 * @see    Storage\MailChimp_Lists_Query()->insert();
	 * @see    Storage\MailChimp_Lists_Query()->query();
	 *
	 * @return void
	 */
	public function test_insert() {

		$saved_data = $this->lists_query->query();

		/**
		 * Assuming that the last 2 of the data are invalid.
		 *
		 * The data with the list ID of '827304a9a2a' would still be
		 * inserted to the table since the 'rand' column will be
		 * filtered-out before inserting the data to the table.
		 */
		$this->assertEquals( 4, count( $saved_data ) );

		$saved_data_sorted = [];
		foreach ( $saved_data as $data ) {
			$saved_data_sorted[ $data['list_id'] ] = $data['name'];
		}

		$this->assertTrue( array_key_exists( '520524cb3b', $saved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 1', $saved_data_sorted['520524cb3b'] );

		$this->assertTrue( array_key_exists( '320424cb3b', $saved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 2', $saved_data_sorted['320424cb3b'] );

		$this->assertTrue( array_key_exists( '610424aa1c', $saved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 3', $saved_data_sorted['610424aa1c'] );

		// Ommitted because the 'name' is empty.
		$this->assertFalse( array_key_exists( '729404aa1c', $saved_data_sorted ) );

		$this->assertTrue( array_key_exists( '827304a9a2', $saved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 6', $saved_data_sorted['827304a9a2'] );
	}

	/**
	 * Test method to get list of the MailChimp IDs
	 *
	 * @since  0.1.0
	 * @see    Storage\MailChimp_Lists_Query()->get_list_ids();
	 *
	 * @return void
	 */
	public function test_get_the_ids() {

		$mailchimp_list_ids = $this->lists_query->get_the_ids();

		$this->assertTrue( is_array( $mailchimp_list_ids ) );
		$this->assertEquals( [ '320424cb3b', '520524cb3b', '610424aa1c', '827304a9a2' ], $mailchimp_list_ids );
	}

	/**
	 * Test method to get the MailChimp list by the list_id
	 *
	 * @since  0.1.0
	 * @see    Storage\MailChimp_Lists_Query()->get_by_the_id();
	 *
	 * @return void
	 */
	public function test_get_by_the_id() {

		$list = $this->lists_query->get_by_the_id( '520524cb3b' );
		$this->assertEquals( [
			'list_id'      => '520524cb3b',
			'name'         => 'MailChimp List 1',
			'subscribers'  => 100,
			'double_optin' => 0,
		], $list );
	}

	/**
	 * Test method to update the existing MailChimp list ID in the database
	 *
	 * @since  0.1.0
	 * @see    Storage\MailChimp_Lists_Query()->update();
	 *
	 * @return void
	 */
	public function test_update() {

		$new_data = [
			'list_id'      => '520524cb3b',
			'name'         => 'MailChimp List Updated 1.1',
			'subscribers'  => 230,
			'double_optin' => 1,
		];

		// Update the list data.
		$updated = $this->lists_query->update( '520524cb3b', $new_data );
		$this->assertEquals( 1, $updated );

		// Get the updated data from the list.
		$list = $this->lists_query->get_by_the_id( '520524cb3b' );
		$this->assertEquals( 'MailChimp List Updated 1.1', $list['name'] );
		$this->assertEquals( '230', $list['subscribers'] );
		$this->assertEquals( '1', $list['double_optin'] );
	}

	/**
	 * Test the method to delete the existing MailChimp list ID in the database
	 *
	 * @since 0.1.0
	 * @see   Storage\MailChimp_Lists_Query()->delete();
	 *
	 * @return void
	 */
	public function test_delete() {

		$deleted = $this->lists_query->delete( '520524cb3b' );
		$this->assertEquals( 1, $deleted ); // The affected arrow should only one.

		// Check the data; it should no longer be in the table.
		$list = $this->lists_query->get_by_the_id( '520524cb3b' );
		$this->assertTrue( is_array( $list ) );
		$this->assertEmpty( $list );
	}

	/**
	 * Test the method to empty the MailChimp list ID in the database
	 *
	 * @since 0.1.0
	 * @see   Storage\MailChimp_Lists_Query()->truncate();
	 *
	 * @return void
	 */
	public function test_truncate() {

		$emptied = $this->lists_query->truncate();
		$this->assertTrue( $emptied );

		$saved_data = $this->lists_query->query();
		$this->assertEquals( 0, count( $saved_data ) );
	}
}
