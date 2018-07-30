<?php
/**
 * PHPUnit Tests: Subscription_Form
 *
 * @package WP_Chimp/Tests/Subscription_Form
 * @since 0.3.0
 */

namespace WP_Chimp\Tests\Subscription_Form;

use WP_Chimp\Tests\UnitTestCase;
use WP_Chimp\Subscription_Form\Subscription_Form;

use Brain\Monkey\Functions;

/**
 * Class to test Subscription_Form class defined under WP_Chimp\Subscription_Form.
 *
 * @since 0.3.0
 */
class Test_Subscription_Form extends UnitTestCase {

	/**
	 * Test the class instance.
	 *
	 * @since 0.3.0
	 */
	public function test_class() {
		$this->assertTrue( is_subclass_of( Subscription_Form::class, 'WP_Chimp\\Core\\Plugin_Base' ) );
	}
}
