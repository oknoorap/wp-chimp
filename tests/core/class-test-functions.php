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
}
