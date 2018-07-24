<?php
/**
 * PHPUnit Tests: Settings
 *
 * @package WP_Chimp/Tests/Core
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Core;

use WP_Chimp\Tests\UnitTestCase;
use WP_Chimp\Core\Settings;

/**
 * Class to test Settings class defined under WP_Chimp\Core.
 *
 * @since 0.3.0
 */
class Test_Settings extends UnitTestCase {

	/**
	 * Test the class instance.
	 *
	 * @since 0.3.0
	 */
	public function test_class() {
		$this->assertTrue( is_subclass_of( Settings::class, 'WP_Chimp\\Core\\Plugin_Base' ) );
	}
}
