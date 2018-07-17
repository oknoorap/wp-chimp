<?php
/**
 * PHPUnit Tests: Functions
 *
 * @package WP_Chimp/Tests/Core
 * @since 0.1.0
 */

namespace WP_Chimp\Tests\Core;

use WP_Chimp\Tests\UnitTestCase;
use WP_Chimp\Core;
use Brain\Monkey\Functions;

/**
 * Class to test core functions defined under WP_Chimp\Core.
 *
 * @since 0.1.0
 */
class Test_Functions extends UnitTestCase {

	/**
	 * MailChimp API response example.
	 *
	 * We are expecting that the response will be persistent,
	 * if MailChimp response is changed the function may
	 * fail without us realising it.
	 *
	 * @var array
	 */
	const RAW_DATA = [
		'lists' => [
			[
				'id' => '520524cb3b',
				'name' => 'Hello World List',
				'date_created' => '2016-07-29T02:40:32+00:00',
				'list_rating' => 0,
				'email_type_option' => false,
				'visibility' => 'pub',
				'double_optin' => false,
				'modules' => [],
				'stats' => [
					'member_count' => 446,
					'unsubscribe_count' => 0,
				],
			],
			[
				'id' => '610424aa1c',
				'name' => 'Foo Bar List',
				'date_created' => '2016-07-29T02:40:32+00:00',
				'list_rating' => 0,
				'email_type_option' => false,
				'visibility' => 'pub',
				'double_optin' => true,
				'modules' => [],
				'stats' => [
					'member_count' => 120,
					'unsubscribe_count' => 0,
				],
			],
		],
	];

	/**
	 * Test the function to sort raw data from MailChimp API response
	 *
	 * @since 0.1.0
	 */
	public function test_sort_mailchimp_lists() {

		$data = Core\sort_mailchimp_lists( self::RAW_DATA['lists'] );
		$this->assertEquals(
			[
				[
					'list_id' => '520524cb3b',
					'name' => 'Hello World List',
					'subscribers' => 446,
					'double_optin' => 0,
				],
				[
					'list_id' => '610424aa1c',
					'name' => 'Foo Bar List',
					'subscribers' => 120,
					'double_optin' => 1,
				],
			], $data
		);
	}

	/**
	 * Test the function to set and get the default list.
	 *
	 * @since 0.2.0
	 */
	public function test_the_default_list() {

		$lists = Core\sort_mailchimp_lists( self::RAW_DATA['lists'] );

		Functions\expect( 'update_option' )
			->once()
			->with( 'wp_chimp_lists_default', $lists[0]['list_id'] )
			->andReturn( true );
		Core\set_the_default_list( $lists );

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_lists_default', '' )
			->andReturn( $lists[0]['list_id'] );

		$this->assertEquals( '520524cb3b', Core\get_the_default_list() );
	}

	/**
	 * Test the function to set and get the default list with custom index.
	 *
	 * @since 0.2.0
	 */
	public function test_the_default_list_index() {

		$lists = Core\sort_mailchimp_lists( self::RAW_DATA['lists'] );
		$index = 1;

		Functions\expect( 'update_option' )
			->once()
			->with( 'wp_chimp_lists_default', $lists[ $index ]['list_id'] )
			->andReturn( true );

		Core\set_the_default_list( $lists, $index );

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_lists_default', '' )
			->andReturn( $lists[ $index ]['list_id'] );

		$this->assertEquals( '610424aa1c', Core\get_the_default_list() );
	}

	/**
	 * Test the function to set and get the default list invalid value.
	 *
	 * @since 0.2.0
	 */
	public function test_the_default_list_invalid() {

		$lists = Core\sort_mailchimp_lists( self::RAW_DATA['lists'] );
		$index = 3; // An invalid index; there's only 2 on the list.

		Functions\expect( 'update_option' )->never();
		Core\set_the_default_list( $lists, $index );

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_lists_default', '' )
			->andReturn( '' );

		$this->assertEquals( '', Core\get_the_default_list() );
	}

	/**
	 * Test the function to obfuscate half of the string.
	 *
	 * @since 0.2.0
	 */
	public function test_obfuscate_string() {

		$string = Core\obfuscate_string( '4G8Cuqgy94e7caG9VqSTv4Gy9hqTq8zYbskMmENj' );
		$this->assertEquals( '********************v4Gy9hqTq8zYbskMmENj', $string );
	}

	/**
	 * Test the function to obfuscate half of the string with an invalid data.
	 *
	 * @since 0.2.0
	 */
	public function test_obfuscate_string_invalid() {

		$string_empty = Core\obfuscate_string( '' );
		$this->assertEmpty( $string_empty );

		$string_null = Core\obfuscate_string( null );
		$this->assertEmpty( $string_null );

		$string_array = Core\obfuscate_string( [] );
		$this->assertEmpty( $string_array );
	}
}
