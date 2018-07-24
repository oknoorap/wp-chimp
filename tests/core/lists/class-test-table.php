<?php
/**
 * PHPUnit Tests: Table
 *
 * @package WP_Chimp/Tests/Core/Lists
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Core;

use WP_Chimp\Core\Database;
use WP_Chimp\Core\Lists\Table;
use WP_Chimp\Tests\UnitTestCase;

/**
 * Class to test Table class defined under WP_Chimp\Core\Lists.
 *
 * @since 0.3.0
 */
class Test_Table extends UnitTestCase {

	/**
	 * Test the class methods.
	 *
	 * @since 0.3.0
	 */
	public function test_methods() {

		$this->assertTrue( method_exists( Table::class, 'run' ) );
		$this->assertTrue( method_exists( Table::class, 'set_loader' ) );
	}

	/**
	 * Test the class properties.
	 *
	 * @since 0.3.0
	 */
	public function test_properties() {
		$this->assertClassHasAttribute( 'loader', Table::class );
	}
}
