<?php
/**
 * PHPUnit Tests: Admin\Page
 *
 * @package WP_Chimp/Tests/Admin/Page
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Admin\Partials;

use WP_Chimp\Admin\Partials\Page;
use WP_Chimp\Tests\UnitTestCase;

/**
 * Class to test Page class defined under WP_Chimp\Admin\Partials.
 *
 * @since 0.3.0
 */
class Test_Page extends UnitTestCase {

	/**
	 * Test the class instance.
	 *
	 * @since 0.3.0
	 */
	public function test_class() {
		$this->assertTrue( is_subclass_of( Page::class, 'WP_Chimp\\Core\\Plugin_Base' ) );
	}
}
