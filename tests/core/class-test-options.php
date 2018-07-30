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

	const OPTIONS = [
		'wp_chimp_api_key' => [
			'default' => '',
			'sanitize_callback' => 'WP_Chimp\\Core\\validate_string',
		],
		'wp_chimp_lists_default' => [
			'default' => '',
			'sanitize_callback' => 'WP_Chimp\\Core\\validate_string',
		],
		'wp_chimp_api_key_status' => [
			'default' => 'invalid',
			'sanitize_callback' => 'WP_Chimp\\Core\\validate_api_key_status',
		],
		'wp_chimp_lists_total_items' => [
			'default' => 0,
			'sanitize_callback' => 'absint',
		],
		'wp_chimp_lists_init' => [
			'default' => 0,
			'sanitize_callback' => 'absint',
		],
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

		foreach ( Options::$options as $option_name => $data ) {

			$this->assertArrayHasKey( 'default', $data );
			$this->assertArrayHasKey( 'sanitize_callback', $data );
			$this->assertTrue( is_callable( $data['sanitize_callback'] ) );

			Functions\expect( 'get_option' )
				->once()
				->with( $option_name )
				->andReturn( false );

			Functions\expect( 'add_option' )
				->once()
				->with( $option_name, $data['default'], '', isset( $data['autoload'] ) ? $data['autoload'] : true );
		}

		Options::ensure_options();
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

		foreach ( self::OPTIONS as $option_name => $data ) {

			Functions\expect( 'get_option' )
				->once()
				->with( $option_name, $data['default'] )
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
	 * Test the get method for option that should return a string.
	 *
	 * @since 0.2.0
	 */
	public function test_get_string() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_api_key', '' )
			->andReturn( '7TwW4Zw2Perj6nkp-us1' );

		$returned_value = Options::get( 'wp_chimp_api_key' );
		$this->assertEquals( '7TwW4Zw2Perj6nkp-us1', $returned_value );
	}

	/**
	 * Test the get method for option that should return a string but
	 * the WordPress get_option function return a float (somehow).
	 *
	 * @since 0.2.0
	 */
	public function test_get_string_return_int() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_api_key', '' )
			->andReturn( 123 );

		$returned_value = Options::get( 'wp_chimp_api_key' );
		$this->assertTrue( is_string( $returned_value ) );
		$this->assertEquals( '123', $returned_value );
	}

	/**
	 * Test the get method for option that should return a string but
	 * the WordPress get_option function return a float (somehow).
	 *
	 * @since 0.2.0
	 */
	public function test_get_string_return_float() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_api_key', '' )
			->andReturn( 1.23 );

		$returned_value = Options::get( 'wp_chimp_api_key' );
		$this->assertTrue( is_string( $returned_value ) );
		$this->assertEquals( '1.23', $returned_value );
	}

	/**
	 * Test the get method for option that should return a string but
	 * the WordPress get_option function return a boolean (somehow).
	 *
	 * @since 0.2.0
	 */
	public function test_get_string_return_bool() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_api_key', '' )
			->andReturn( true );

		$returned_value = Options::get( 'wp_chimp_api_key' );
		$this->assertTrue( is_string( $returned_value ) );
		$this->assertEquals( '1', $returned_value );
	}

	/**
	 * Test the get method for option that should return a string but
	 * the WordPress get_option function return a boolean (somehow).
	 *
	 * @since 0.2.0
	 */
	public function test_get_int() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_lists_total_items', 0 )
			->andReturn( 1 );

		$returned_value = Options::get( 'wp_chimp_lists_total_items' );
		$this->assertTrue( is_int( $returned_value ) );
		$this->assertEquals( 1, $returned_value );
	}

	/**
	 * Test the get method for option that should return a string but
	 * the WordPress get_option function return a boolean (somehow).
	 *
	 * @since 0.2.0
	 */
	public function test_get_int_return_bool() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_lists_total_items', 0 )
			->andReturn( true );

		$returned_value = Options::get( 'wp_chimp_lists_total_items' );
		$this->assertTrue( is_int( $returned_value ) );
		$this->assertEquals( 1, $returned_value );
	}

	/**
	 * Test the get method for option that should return a string but
	 * the WordPress get_option function return a float (somehow).
	 *
	 * @since 0.2.0
	 */
	public function test_get_int_return_float() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_lists_total_items', 0 )
			->andReturn( 1.23 );

		$returned_value = Options::get( 'wp_chimp_lists_total_items' );
		$this->assertTrue( is_int( $returned_value ) );
		$this->assertEquals( 1, $returned_value );
	}

	/**
	 * Test the get method for option that should return a string but
	 * the WordPress get_option function return a string (somehow).
	 *
	 * @since 0.2.0
	 */
	public function test_get_int_return_string() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_lists_total_items', 0 )
			->andReturn( '1' );

		$returned_value = Options::get( 'wp_chimp_lists_total_items' );
		$this->assertTrue( is_int( $returned_value ) );
		$this->assertEquals( 1, $returned_value );
	}

	/**
	 * Test the api key status option.
	 *
	 * @since 0.2.0
	 */
	public function test_get_api_key_status() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_api_key_status', 'invalid' )
			->andReturn( 'valid' );

		$returned_value = Options::get( 'wp_chimp_api_key_status' );
		$this->assertEquals( 'valid', $returned_value );
	}

	/**
	 * Test the api key status option with an invalid returned valud.
	 *
	 * @since 0.2.0
	 */
	public function test_get_api_key_status_return_invalid() {

		Functions\expect( 'get_option' )
			->once()
			->with( 'wp_chimp_api_key_status', 'invalid' )
			->andReturn( 1 ); // Return a boolean. Actually should only return 'valid' and 'invalid'.

		$returned_value = Options::get( 'wp_chimp_api_key_status' );
		$this->assertEquals( 'invalid', $returned_value );
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
