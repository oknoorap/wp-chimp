<?php
/**
 * PHPUnit Tests: Admin\Page
 *
 * @package WP_Chimp/Tests/Admin/Page
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Admin;

use WP_Chimp\Admin\Admin;
use WP_Chimp\Tests\UnitTestCase;

/**
 * Class to test the main Admin class defined under WP_Chimp\Admin.
 *
 * @since 0.3.0
 */
class Test_Admin extends UnitTestCase {

	/**
	 * Test the class instance.
	 *
	 * @since 0.3.0
	 */
	public function test_class() {
		$this->assertTrue( is_subclass_of( Admin::class, 'WP_Chimp\\Core\\Plugin_Base' ) );
	}
}
