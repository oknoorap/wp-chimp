<?php
/**
 * PHPUnit Tests: WP_Chimp\Core\Lists\Query
 *
 * @package WP_Chimp/Tests/Core/Lists;
 * @since 0.1.0
 */

namespace WP_Chimp\Tests\Core\Lists;

use WP_Chimp\Core;
use WP_Chimp\Tests\UnitTestCase;
use Brain\Monkey\Functions;
use Mockery;

/**
 * The class to test the "WP_Chimp\Core\Lists\Query" instance.
 *
 * @since 0.1.0
 */
class Test_Query extends UnitTestCase {

	/**
	 * The cache key to save the lists in the Object Caching.
	 *
	 * @since 0.3.0
	 */
	const CACHE_KEY = 'lists';

	/**
	 * The cache group.
	 *
	 * @since 0.3.0
	 */
	const CACHE_GROUP = 'wp_chimp_lists';

	/**
	 * The lists sample data
	 *
	 * @since 0.1.0
	 * @var array
	 */
	protected $sample_data;

	/**
	 * The Query instance
	 *
	 * @since 0.1.0
	 * @var WP_Chimp\Core\Lists\Query
	 */
	protected $lists_query;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

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

		$this->lists_query = new Core\Lists\Query();

		foreach ( $this->sample_data as $data ) { // Insert the data to the table.
			$this->lists_query->insert( $data );
		}
	}

	/**
	 * Test method to count the number of rows in the table
	 *
	 * @since 0.1.0
	 * @see Lists\Query()->count_rows();
	 */
	public function test_count_rows() {
		$this->assertEquals( 4, $this->lists_query->count_rows() );
	}

	/**
	 * Test method to insert a new entry to the database.
	 *
	 * @since 0.1.0
	 * @see self::setUp()
	 * @see WP_Chimp\Core\Lists\Query()->insert();
	 * @see WP_Chimp\Core\Lists\Query()->query();
	 */
	public function test_insert() {

		Functions\when( 'WP_Chimp\\Core\\get_the_lists_total_items' )->justReturn( count( $this->sample_data ) ); // 7.
		$saved_data = $this->lists_query->query();

		/**
		 * Assuming that the last 2 of the data are invalid.
		 *
		 * The data with the list ID of '827304a9a2a' would still be
		 * inserted to the table since the 'rand' column will be
		 * filtered-out before inserting the data to the table.
		 */
		$this->assertCount( 4, $saved_data );

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

		$this->assertFalse( array_key_exists( '729404aa1c', $saved_data_sorted ) ); // Ommitted because the 'name' is empty.

		$this->assertTrue( array_key_exists( '827304a9a2', $saved_data_sorted ) );
		$this->assertEquals( 'MailChimp List 6', $saved_data_sorted['827304a9a2'] );
	}

	/**
	 * Test method to insert an empty data.
	 *
	 * @since 0.1.0
	 * @see self::setUp()
	 * @see WP_Chimp\Core\Lists\Query()->insert();
	 * @see WP_Chimp\Core\Lists\Query()->query();
	 */
	public function test_insert_empty_data() {

		$inserted = $this->lists_query->insert( [] );

		$this->assertInstanceOf( 'WP_Error', $inserted );
		$this->assertTrue( property_exists( $inserted, 'errors' ) );
		$this->assertTrue( array_key_exists( 'wp_chimp_list_data_invalid', $inserted->errors ) );
	}

	/**
	 * Test method to insert an entry that has invalid list_id.
	 *
	 * @since 0.1.0
	 * @see self::setUp()
	 * @see WP_Chimp\Core\Lists\Query()->insert();
	 * @see WP_Chimp\Core\Lists\Query()->query();
	 */
	public function test_insert_invalid_list_id() {

		$inserted = $this->lists_query->insert(
			[
				'list_id'      => 123, // Invalid data. Should be a string.
				'name'         => 'MailChimp List 10',
				'subscribers'  => 300,
				'double_optin' => 1,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
			]
		);

		$this->assertInstanceOf( 'WP_Error', $inserted );
		$this->assertTrue( property_exists( $inserted, 'errors' ) );
		$this->assertTrue( array_key_exists( 'wp_chimp_list_data_invalid', $inserted->errors ) );
	}

	/**
	 * Test method to insert an entry that has invalid name.
	 *
	 * @since 0.1.0
	 * @see self::setUp()
	 * @see WP_Chimp\Core\Lists\Query()->insert();
	 * @see WP_Chimp\Core\Lists\Query()->query();
	 */
	public function test_insert_invalid_name() {

		$inserted = $this->lists_query->insert(
			[
				'list_id'      => '1039jdh83k',
				'name'         => '', // Invalid data. Should be a string.
				'subscribers'  => 310,
				'double_optin' => 0,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
			]
		);

		$this->assertInstanceOf( 'WP_Error', $inserted );
		$this->assertTrue( property_exists( $inserted, 'errors' ) );
		$this->assertTrue( array_key_exists( 'wp_chimp_list_data_invalid', $inserted->errors ) );
	}

	/**
	 * Test method to insert bad entry to the database.
	 *
	 * @since 0.1.0
	 * @see self::setUp()
	 * @see WP_Chimp\Core\Lists\Query()->insert();
	 * @see WP_Chimp\Core\Lists\Query()->query();
	 */
	public function test_insert_existing_id() {

		$inserted_exists = $this->lists_query->insert(
			[ // Test inserting list id that already exists.
				'list_id'      => '520524cb3b',
				'name'         => 'MailChimp List 1',
				'subscribers'  => 100,
				'double_optin' => 0,
				'synced_at'    => date( 'Y-m-d H:i:s' ),
			]
		);

		$this->assertInstanceOf( 'WP_Error', $inserted_exists );
		$this->assertTrue( property_exists( $inserted_exists, 'errors' ) );
		$this->assertTrue( array_key_exists( 'wp_chimp_list_id_exists', $inserted_exists->errors ) );
	}

	/**
	 * Test method to get list of the MailChimp IDs
	 *
	 * @since 0.1.0
	 * @see WP_Chimp\Core\Lists\Query()->get_list_ids();
	 */
	public function test_get_the_ids() {

		$mailchimp_list_ids = $this->lists_query->get_the_ids();

		$this->assertTrue( is_array( $mailchimp_list_ids ) );
		$this->assertEquals( [ '320424cb3b', '520524cb3b', '610424aa1c', '827304a9a2' ], $mailchimp_list_ids );
	}

	/**
	 * Test method to get the MailChimp list by the list_id
	 *
	 * @since 0.1.0
	 * @see WP_Chimp\Core\Lists\Query()->get_by_the_id();
	 */
	public function test_get_by_the_id() {

		$list = $this->lists_query->get_by_the_id( '520524cb3b' );

		$this->assertEquals(
			[
				'list_id'      => '520524cb3b',
				'name'         => 'MailChimp List 1',
				'subscribers'  => 100,
				'double_optin' => 0,
			], $list
		);
	}

	/**
	 * Test method to update the existing MailChimp list ID in the database
	 *
	 * @since 0.1.0
	 * @see WP_Chimp\Core\Lists\Query()->update();
	 */
	public function test_update() {

		$new_data = [
			'list_id' => '520524cb3b',
			'name' => 'MailChimp List Updated 1.1',
			'subscribers'  => 230,
			'double_optin' => 1,
		];

		$updated = $this->lists_query->update( '520524cb3b', $new_data ); // Update the list data.

		$this->assertEquals( 1, $updated );

		$list = $this->lists_query->get_by_the_id( '520524cb3b' ); // Get the updated data from the list.

		$this->assertEquals( 'MailChimp List Updated 1.1', $list['name'] );
		$this->assertEquals( '230', $list['subscribers'] );
		$this->assertEquals( '1', $list['double_optin'] );
	}

	/**
	 * Test the method to delete the existing MailChimp list ID in the database
	 *
	 * @since 0.1.0
	 * @see WP_Chimp\Core\Lists\Query()->delete();
	 */
	public function test_delete() {

		$deleted = $this->lists_query->delete( '520524cb3b' );  // The affected arrow should only one.

		$this->assertEquals( 1, $deleted );

		$list = $this->lists_query->get_by_the_id( '520524cb3b' ); // Check the data; it should no longer be in the table.

		$this->assertTrue( is_array( $list ) );
		$this->assertEmpty( $list );
	}

	/**
	 * Test the method to empty the MailChimp list ID in the database
	 *
	 * @since 0.1.0
	 * @see WP_Chimp\Core\Lists\Query()->truncate();
	 */
	public function test_truncate() {

		$emptied = $this->lists_query->truncate();
		$this->assertTrue( $emptied );

		$saved_data = $this->lists_query->query();
		$this->assertEquals( 0, count( $saved_data ) );
	}

	/**
	 * Test the method to retrieve the cache lists
	 *
	 * @since 0.3.0
	 */
	public function test_get_cache() {

		$cache_output = [
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
		];

		Functions\expect( 'wp_cache_get' )
			->once()
			->with( self::CACHE_KEY, self::CACHE_GROUP )
			->andReturn( $cache_output );

		$cache = $this->lists_query->get_cache();

		$this->assertInternalType( 'array', $cache );
		$this->assertEquals( $cache_output, $cache );
	}

	/**
	 * Test the method to retrieve the cache lists with an invalid output
	 *
	 * @since 0.3.0
	 */
	public function test_get_cache_invalid() {

		Functions\expect( 'wp_cache_get' )
			->once()
			->with( self::CACHE_KEY, self::CACHE_GROUP )
			->andReturn( true ); // Output must be an array containing the lists.

		$cache = $this->lists_query->get_cache();

		$this->assertInternalType( 'array', $cache );
		$this->assertEmpty( $cache );
	}

	/**
	 * Test the method to delete the cache
	 *
	 * @since 0.3.0
	 */
	public function test_delete_cache() {

		Functions\expect( 'wp_cache_delete' )
			->once()
			->with( self::CACHE_KEY, self::CACHE_GROUP )
			->andReturn( true );

		$cache = $this->lists_query->delete_cache();
		$this->assertTrue( $cache );
	}

	/**
	 * Test the method to add the cache
	 *
	 * @return void
	 */
	public function test_add_cache() {

		$value = [
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
		];

		Functions\expect( 'wp_cache_add' )
			->once()
			->with( self::CACHE_KEY, $value, self::CACHE_GROUP )
			->andReturn( true );

		$cache = $this->lists_query->add_cache( $value );
		$this->assertTrue( $cache );
	}

	/**
	 * Test the method to add the cache
	 *
	 * @return void
	 */
	public function test_set_cache() {

		$value = [
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
		];

		Functions\expect( 'wp_cache_set' )
			->once()
			->with( self::CACHE_KEY, $value, self::CACHE_GROUP )
			->andReturn( true );

		$cache = $this->lists_query->set_cache( $value );
		$this->assertTrue( $cache );
	}
}
