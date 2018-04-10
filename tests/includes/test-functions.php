<?php
/**
 * Class Test_Query
 *
 * @package WP_Chimp\Tests;
 */

namespace WP_Chimp;

// Load WP_UnitTestCase.
use WP_UnitTestCase;

/**
 * The class to test the "Utilities" functions.
 *
 * @since 1.2.3
 */
class Test_Functions extends WP_UnitTestCase {

	/**
	 * The MailChimp API response
	 *
	 * @var array
	 */
	protected $raw_data;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();

		/**
		 * We are expecting that the response will be persistent,
		 * if MailChimp response is changed the function may
		 * fail without us realising it.
		 *
		 * @var array
		 */
		$this->raw_data = [
			'lists' => [
				[
					'id'                => '520524cb3b',
					'name'              => 'Hello World List',
					'date_created'      => '2016-07-29T02:40:32+00:00',
					'list_rating'       => 0,
					'email_type_option' => false,
					'visibility'        => 'pub',
					'double_optin'      => false,
					'modules'           => [],
					'stats'             => [
						'member_count'      => 446,
						'unsubscribe_count' => 0,
					],
				],
				[
					'id'                => '610424aa1c',
					'name'              => 'Foo Bar List',
					'date_created'      => '2016-07-29T02:40:32+00:00',
					'list_rating'       => 0,
					'email_type_option' => false,
					'visibility'        => 'pub',
					'double_optin'      => true,
					'modules'           => [],
					'stats'             => [
						'member_count'      => 120,
						'unsubscribe_count' => 0,
					],
				],
			],
		];
	}

	/**
	 * Test the function to sort raw data from MailChimp API response
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_sort_mailchimp_data() {
		$data = sort_mailchimp_data( $this->raw_data['lists'] );
		$this->assertEquals( [
			[
				'list_id'      => '520524cb3b',
				'name'         => 'Hello World List',
				'subscribers'  => 446,
				'double_optin' => 0,
			],
			[
				'list_id'      => '610424aa1c',
				'name'         => 'Foo Bar List',
				'subscribers'  => 120,
				'double_optin' => 1,
			],
		], $data );
	}
}
