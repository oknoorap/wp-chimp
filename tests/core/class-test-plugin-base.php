<?php
/**
 * PHPUnit Tests: Plugin_Base
 *
 * @package WP_Chimp/Tests/Core
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Core;

use WP_Chimp\Core\Loader;
use WP_Chimp\Core\Plugin_Base;
use WP_Chimp\Tests\UnitTestCase;

use Brain\Monkey\Functions;

/**
 * Class to test Plugin_Base class defined under WP_Chimp\Core.
 *
 * @since 0.3.0
 */
class Test_Plugin_Base extends UnitTestCase {

	/**
	 * Test the class methods.
	 *
	 * @since 0.3.0
	 */
	public function test_methods() {

		$this->assertTrue( method_exists( Plugin_Base::class, 'run' ) );
		$this->assertTrue( method_exists( Plugin_Base::class, 'set_loader' ) );
		$this->assertTrue( method_exists( Plugin_Base::class, 'get_version' ) );
		$this->assertTrue( method_exists( Plugin_Base::class, 'get_plugin_name' ) );
		$this->assertTrue( method_exists( Plugin_Base::class, 'get_file_path' ) );
		$this->assertTrue( method_exists( Plugin_Base::class, 'get_loader' ) );
	}

	/**
	 * Test the class properties.
	 *
	 * @since 0.3.0
	 */
	public function test_properties() {

		$this->assertClassHasAttribute( 'version', Plugin_Base::class );
		$this->assertClassHasAttribute( 'loader', Plugin_Base::class );
		$this->assertClassHasAttribute( 'plugin_name', Plugin_Base::class );
		$this->assertClassHasAttribute( 'file_path', Plugin_Base::class );
	}

	/**
	 * Test the class properties type.
	 *
	 * @since 0.3.0
	 */
	public function test_properties_type() {

		$loader = $this->getMockBuilder( Loader::class )->getMock();
		$plugin_base = $this->getMockForAbstractClass( Plugin_Base::class, [ 'wp-chimp', '0.0.0', 'path/file.php' ] );
		$plugin_base->set_loader( $loader );

		$this->assertInternalType( 'string', $plugin_base->get_plugin_name() );
		$this->assertEquals( 'wp-chimp', $plugin_base->get_plugin_name() );

		$this->assertInternalType( 'string', $plugin_base->get_version() );
		$this->assertEquals( '0.0.0', $plugin_base->get_version() );

		$this->assertInternalType( 'string', $plugin_base->get_file_path() );
		$this->assertEquals( 'path/file.php', $plugin_base->get_file_path() );

		$this->assertInstanceOf( 'WP_Chimp\\Core\\Loader', $plugin_base->get_loader() );
	}
}
