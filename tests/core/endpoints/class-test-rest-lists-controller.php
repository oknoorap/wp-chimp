<?php
/**
 * PHPUnit Tests: REST_Lists_Controller
 *
 * @package WP_Chimp/Tests/Endpoints/REST_Lists_Controller
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Endpoints\REST_Lists_Controller;

use WP_Chimp\Core\Endpoints\REST_Lists_Controller;
use WP_Chimp\Tests\UnitTestCase;

/**
 * Class to test REST_Lists_Controller class defined under WP_Chimp\Core\Endpoints.
 *
 * @since 0.3.0
 */
class Test_REST_Lists_Controller extends UnitTestCase {

	/**
	 * Test the class instance.
	 *
	 * @since 0.3.0
	 */
	public function test_class() {
		$this->assertTrue( is_subclass_of( REST_Lists_Controller::class, 'WP_Chimp\\Core\\Endpoints\\REST_Controller' ) );
	}

	/**
	 * Test the namespace method.
	 *
	 * @since 0.3.0
	 */
	public function test_rest_base() {

		$lists_controller = new REST_Lists_Controller( 'wp-chimp', '0.0.0' );
		$this->assertEquals( 'lists', $lists_controller->get_rest_base() );
	}
}
