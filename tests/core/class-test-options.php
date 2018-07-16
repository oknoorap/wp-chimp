<?php
/**
 * PHPUnit Tests: Options
 *
 * @package WP_Chimp/Tests/Core
 * @since 0.1.0
 */

namespace WP_Chimp\Tests\Core;

use WP_Chimp\Tests\UnitTestCase;
use WP_Chimp\Core\Options;
use Brain\Monkey\Functions;

/**
 * Class to test Options class defined under WP_Chimp\Core.
 *
 * @since 0.1.0
 */
class Test_Options extends UnitTestCase {

	const OPTION_NAMES = [
		'wp_chimp_api_key' => '',
		'wp_chimp_lists_default' => '',
		'wp_chimp_api_key_status' => 'invalid',
		'wp_chimp_lists_total_items' => 0,
		'wp_chimp_lists_init' => 0,
	];

	/**
	 * Test the class properties.
	 *
	 * @since 0.2.0
	 */
	public function test_properties() {

		$this->assertClassHasStaticAttribute( 'options', Options::class );
		$this->assertInternalType( 'array', Options::$options );
	}

	/**
	 * Test the class methods.
	 *
	 * @since 0.2.0
	 */
	public function test_methods() {

		$options = new Options();

		$this->assertTrue( method_exists( $options, 'get' ) );
		$this->assertTrue( method_exists( $options, 'add' ) );
		$this->assertTrue( method_exists( $options, 'update' ) );
		$this->assertTrue( method_exists( $options, 'ensure_options' ) );
	}

	/**
	 * Test the option names and their default values.
	 *
	 * @since 0.2.0
	 */
	public function test_default_option_names_and_values() {

		foreach ( self::OPTION_NAMES as $option_name => $value ) {
			$this->assertArrayHasKey( $option_name, Options::$options );
			$this->assertTrue( Options::$options[ $option_name ] === $value );
		}
	}

	/**
	 * Test the get method.
	 *
	 * @since 0.2.0
	 */
	public function test_get() {

		$expected_values = [
			'wp_chimp_api_key' => '7TwW4Zw2Perj6nkp-us1',
			'wp_chimp_lists_default' => 'VYdHT2eVGq',
			'wp_chimp_api_key_status' => 'valid',
			'wp_chimp_lists_total_items' => 1,
			'wp_chimp_lists_init' => 1,
		];

		foreach ( self::OPTION_NAMES as $option_name => $value ) {

			Functions\expect( 'get_option' )
				->once()
				->with( $option_name, $value )
				->andReturn( $expected_values[ $option_name ] );

			$returned_value = Options::get( $option_name );
			$this->assertEquals( $expected_values[ $option_name ], $returned_value );
		}
	}

	/**
	 * Test the get method with an invalid option name.
	 *
	 * @since 0.2.0
	 */
	public function test_get_invalid() {

		Functions\expect( 'get_option' )->never();
		$returned_value = Options::get( 'wp_chimp_api' ); // Option name does not exist.

		$this->assertInstanceOf( \WP_Error::class, $returned_value );
		$this->assertEquals( 'wp-chimp-option-name-invalid', $returned_value->get_error_code() );
		$this->assertEquals( 'The option name is not registered.', $returned_value->get_error_message( 'wp-chimp-option-name-invalid' ) );
	}

	/**
	 * Test the add method.
	 *
	 * @return void
	 */
	public function test_add() {

		$value = '7TwW4Zw2Perj6nkp-us1';

		Functions\expect( 'add_option' )->never();
		Functions\expect( 'update_option' )
			->once()
			->with( 'wp_chimp_api_key', $value )
			->andReturn( true );

		$returned_value = Options::add( 'wp_chimp_api_key', $value );
		$this->assertTrue( $returned_value );
	}

	/**
	 * Test the add method with an invalid option name.
	 *
	 * @since 0.2.0
	 */
	public function test_add_invalid() {

		$value = '7TwW4Zw2Perj6nkp-us1';

		Functions\expect( 'update_option' )->never();
		$returned_value = Options::add( 'wp_chimp_api', $value );

		$this->assertInstanceOf( \WP_Error::class, $returned_value );
		$this->assertEquals( 'wp-chimp-option-name-invalid', $returned_value->get_error_code() );
		$this->assertEquals( 'The option name is not registered.', $returned_value->get_error_message( 'wp-chimp-option-name-invalid' ) );
	}

	/**
	 * Test the update method.
	 *
	 * @since 0.2.0
	 */
	public function test_update() {

		$value = 'zcVepH5jcn-us2';

		Functions\expect( 'add_option' )->never();
		Functions\expect( 'update_option' )
			->once()
			->with( 'wp_chimp_api_key', $value )
			->andReturn( true );

		$returned_value = Options::update( 'wp_chimp_api_key', $value );
		$this->assertTrue( $returned_value );
	}

	/**
	 * Test the add method with an invalid option name.
	 *
	 * @since 0.2.0
	 */
	public function test_update_invalid() {

		$value = 'zcVepH5jcn-us2';

		Functions\expect( 'update_option' )->never();
		$returned_value = Options::update( 'wp_chimp_api_status', $value );

		$this->assertInstanceOf( \WP_Error::class, $returned_value );
		$this->assertEquals( 'wp-chimp-option-name-invalid', $returned_value->get_error_code() );
		$this->assertEquals( 'The option name is not registered.', $returned_value->get_error_message( 'wp-chimp-option-name-invalid' ) );
	}
}
