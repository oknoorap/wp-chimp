<?php
/**
 * PHPUnit Tests: Languages
 *
 * @package WP_Chimp/Tests/Core
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Core;

use WP_Chimp\Core\Languages;
use WP_Chimp\Tests\UnitTestCase;

/**
 * Class to test Languages class defined under WP_Chimp\Core.
 *
 * @since 0.3.0
 */
class Test_Languages extends UnitTestCase {

	/**
	 * Test the class instance.
	 *
	 * @since 0.3.0
	 */
	public function test_class() {
		$this->assertTrue( is_subclass_of( Languages::class, 'WP_Chimp\\Core\\Plugin_Base' ) );
	}
}
