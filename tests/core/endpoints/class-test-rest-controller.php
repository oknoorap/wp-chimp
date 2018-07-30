<?php
/**
 * PHPUnit Tests: REST_Controller
 *
 * @package WP_Chimp/Tests/Endpoints/REST_Controller
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Endpoints\REST_Controller;

use WP_Chimp\Core\Loader;
use WP_Chimp\Core\Endpoints\REST_Controller;
use WP_Chimp\Tests\UnitTestCase;

/**
 * Class to test REST_Controller class defined under WP_Chimp\Core\Endpoints.
 *
 * @since 0.3.0
 */
class Test_REST_Controller extends UnitTestCase {

	/**
	 * Test the class instance.
	 *
	 * @since 0.3.0
	 */
	public function test_class() {
		$this->assertTrue( is_subclass_of( REST_Controller::class, 'WP_REST_Controller' ) );
	}

	/**
	 * Test the class methods.
	 *
	 * @since 0.3.0
	 */
	public function test_methods() {

		$this->assertTrue( method_exists( REST_Controller::class, 'run' ) );
		$this->assertTrue( method_exists( REST_Controller::class, 'set_loader' ) );
		$this->assertTrue( method_exists( REST_Controller::class, 'get_version' ) );
		$this->assertTrue( method_exists( REST_Controller::class, 'get_plugin_name' ) );
		$this->assertTrue( method_exists( REST_Controller::class, 'get_loader' ) );

		$this->assertTrue( method_exists( REST_Controller::class, 'get_namespace' ) );
		$this->assertTrue( method_exists( REST_Controller::class, 'get_rest_base' ) );
		$this->assertTrue( method_exists( REST_Controller::class, 'get_rest_version' ) );

		$this->assertTrue( method_exists( REST_Controller::class, 'set_mailchimp' ) );
		$this->assertTrue( method_exists( REST_Controller::class, 'set_lists_query' ) );
		$this->assertTrue( method_exists( REST_Controller::class, 'set_lists_process' ) );
	}

	/**
	 * Test the class properties.
	 *
	 * @since 0.3.0
	 */
	public function test_properties() {

		$this->assertClassHasAttribute( 'plugin_name', REST_Controller::class );
		$this->assertClassHasAttribute( 'version', REST_Controller::class );
		$this->assertClassHasAttribute( 'loader', REST_Controller::class );

		$this->assertClassHasAttribute( 'namespace', REST_Controller::class );
		$this->assertClassHasAttribute( 'rest_base', REST_Controller::class );

		$this->assertClassHasAttribute( 'mailchimp', REST_Controller::class );
		$this->assertClassHasAttribute( 'lists_query', REST_Controller::class );
		$this->assertClassHasAttribute( 'lists_process', REST_Controller::class );
	}

	/**
	 * Test the class properties type.
	 *
	 * @since 0.3.0
	 */
	public function test_properties_type() {

		$loader = $this->getMockBuilder( Loader::class )->getMock();
		$plugin_base = $this->getMockForAbstractClass( REST_Controller::class, [ 'wp-chimp', '0.0.0' ] );
		$plugin_base->set_loader( $loader );

		$this->assertInternalType( 'string', $plugin_base->get_plugin_name() );
		$this->assertEquals( 'wp-chimp', $plugin_base->get_plugin_name() );

		$this->assertInternalType( 'string', $plugin_base->get_version() );
		$this->assertEquals( '0.0.0', $plugin_base->get_version() );

		$this->assertInstanceOf( 'WP_Chimp\\Core\\Loader', $plugin_base->get_loader() );
	}

	/**
	 * Test the namespace method.
	 *
	 * @since 0.3.0
	 */
	public function test_namespace() {

		$this->assertEquals( 'wp-chimp/v1', REST_Controller::get_namespace() );
	}

	/**
	 * Test the namespace method.
	 *
	 * @since 0.3.0
	 */
	public function test_rest_version() {

		$this->assertEquals( 'v1', REST_Controller::get_rest_version() );
	}
}
