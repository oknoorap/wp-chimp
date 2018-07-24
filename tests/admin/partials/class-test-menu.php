<?php
/**
 * PHPUnit Tests: Admin\Menu
 *
 * @package WP_Chimp/Tests/Admin/Menu
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Admin\Partials;

use WP_Chimp\Admin\Partials\Menu;
use WP_Chimp\Tests\UnitTestCase;

/**
 * Class to test Menu class defined under WP_Chimp\Admin\Partials.
 *
 * @since 0.3.0
 */
class Test_Menu extends UnitTestCase {

	/**
	 * Test the class instance.
	 *
	 * @since 0.3.0
	 */
	public function test_class() {
		$this->assertTrue( is_subclass_of( Menu::class, 'WP_Chimp\\Core\\Plugin_Base' ) );
	}
}
