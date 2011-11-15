<?php
require_once( dirname( __FILE__ ) . "/Dio.php" );
use CenaDTA\Dio\Dio as Dio;
define( 'WORDY', 0 );

class DioDioTest extends PHPUnit_Framework_TestCase
{
	// +----------------------------------------------------------------------+
	public function setUp()
	{
	}
	public function test_min()
	{
	}
	// +----------------------------------------------------------------------+
	// test suites for htmlForm.
	// +----------------------------------------------------------------------+
	public function test_filter_method()
	{
		// convert a text to upper case. 
		$input  = 'a text';
		$origin = $input;
		$return = Dio::filter( $input, 'upper', TRUE, $error );
		$this->assertTrue( $return );
		$this->assertEquals( strtoupper( $origin ), $input );
		
		// verify a text is all lower case. 
		$input  = 'a text';
		$return = Dio::filter( $input, 'pattern', '[ a-z]*' );
		$this->assertTrue( $return );
		
		// verify a text is not all upper case. and check error message.
		$input  = 'a text';
		$err_msg= 'only upper case';
		$return = Dio::filter( $input, 'pattern', '[A-Z]*', $error, $err_msg );
		$this->assertFalse( $return );
		$this->assertEquals( $err_msg, $error );
	}
	// +----------------------------------------------------------------------+
	public function test_multiple_staff()
	{
        $source = array( 
            'date_y' => '2011',
            'date_m' => '11',
            'date_d' => '15'
        );
        $correct = '2011-11-15';
        $option = array(
            'separator' => '_',
            'connecter' => '-',
            'suffix'    => array( 'y', 'm', 'd' )
        );
        // test multiple method
        $found = Dio::multiple( $source, 'date', $option );
		$this->assertEquals( $correct, $found );
        
        // test input type=date
        $return = Dio::poke( $source, 'date', $value, 'date', array(), $error );
		$this->assertEquals( $correct, $value );
		$this->assertTrue( $return );
        
        // test bad input having NULL
        $bad_source = array( 
            'date_y' => '20' . chr(0) . '11',
            'date_m' => '11',
            'date_d' => '15'
        );
        $return = Dio::poke( $bad_source, 'date', $value, 'date', array(), $error );
		$this->assertEquals( $correct, $value );
		$this->assertTrue( $return );
	}
	// +----------------------------------------------------------------------+
	public function test_verify_method()
	{
		$input  = 'a text';
		$origin = $input;
		$return = Dio::verify( $input, 'text', array(), $error );
		$this->assertTrue( $return );
		$this->assertEquals( $origin, $input );
		
		$input  = 'a text';
		$origin = $input;
		$return = Dio::verify( $input, 'asis', array(), $error );
		$this->assertTrue( $return );
		$this->assertEquals( $origin, $input );
		
		$input  = "a " . chr(0) . " text";
		$origin = $input;
		$return = Dio::verify( $input, 'asis', array(), $error );
		$this->assertTrue( $return );
		$this->assertNotEquals( $origin, $input );
		$this->assertEquals( str_replace( "\0", '', $origin ), $input );
		
		$input  = 'boGus@eXample.com';
		$origin = $input;
		$return = Dio::verify( $input, 'mail', array(), $error );
		$this->assertTrue( $return );
		$this->assertNotEquals( $origin, $input );
		$this->assertEquals( strtolower( $origin ), $input );
		
		$input  = 'a text';
		$origin = $input;
		$return = Dio::verify( $input, 'mail', array(), $error );
		$this->assertFalse( $return );
		
		$input  = '100';
		$return = Dio::verify( $input, 'number', array(), $error );
		$this->assertTrue( $return );
		
		$input  = '１００';
		$origin = $input;
		$return = Dio::verify( $input, 'number', array(), $error );
		$this->assertTrue( $return );
		$this->assertNotEquals( $origin, $input );
		$this->assertEquals( mb_convert_kana( $origin, 'aks', 'UTF-8' ), $input );
		
		$input  = '-100.0';
		$return = Dio::verify( $input, 'number', array(), $error );
		$this->assertFalse( $return );
		
		$input  = '-100.0';
		$return = Dio::verify( $input, 'float', array(), $error );
		$this->assertTrue( $return );
	}
	// +----------------------------------------------------------------------+
	public function test_sameas_staff()
	{
		$input    = 'same as';
		$same_str = $input;
		$diff_str = 'diff-rent';
		$return = Dio::filter( $input, 'sameas', $same_str, $error );
		$this->assertTrue( $return );
		
		$return = Dio::filter( $input, 'sameas', $diff_str, $error );
		$this->assertFalse( $return );
		
		$return = Dio::verify( $input, 'asis', array(
				'sameas' => 'same as'
			), $error );
		$this->assertTrue( $return );
		
		$return = Dio::verify( $input, 'asis', array(
			'sameas' => 'different'
		), $error );
		$this->assertFalse( $return );
		$this->assertEquals( Dio::$default_err_msgs[ 'sameas' ], $error );
		
	}
	// +----------------------------------------------------------------------+
}




?>