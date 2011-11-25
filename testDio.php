<?php
error_reporting( E_ALL );
require_once( dirname( __FILE__ ) . "/Validator.php" );
use CenaDTA\Util\Validator as Validator;
define( 'WORDY', 0 );

class DioDioTest extends PHPUnit_Framework_TestCase
{
	// +----------------------------------------------------------------------+
	public function setUp()
	{
	}
	public function test_min()
	{
        $input  = ' a test ';
		$origin = $input;
		$return = Validator::validate( $input, 'text', array(), $error );
		$this->assertTrue( $return );
		$this->assertNotEquals( $origin, $input );
		$this->assertEquals( trim( $origin ), $input );
        //$this->assertTrue( phpversion() );
	}
	// +----------------------------------------------------------------------+
	// test suites for htmlForm.
	// +----------------------------------------------------------------------+
	public function test_find_method()
	{
        // simply find a value.
        $name = 'test';
        $value = 'test value';
        $data = array(
            $name => $value,
        );
        $found = Validator::_find( $data, $name );
		$this->assertEquals( $value, $found );
        
        // look for non-existent value, should return FALSE.
        $found = Validator::_find( $data, 'bad_name' );
		$this->assertNotEquals( $value, $found );
		$this->assertFalse( $found );
        
        // look for non-string value (FALSE), should return ''. 
        $name = 'test';
        $value = FALSE;
        $data = array(
            $name => $value,
        );
        $found = Validator::_find( $data, $name );
		$this->assertTRUE( '' === $found );
        
        // make sure find can find '0', and returns '0'.
        $name = 'test';
        $value = 0;
        $data = array(
            $name => $value,
        );
        $found = Validator::_find( $data, $name );
		$this->assertEquals( $value, $found );
		$this->assertTRUE( "$value" === "$found" );
    }
	// +----------------------------------------------------------------------+
	public function test_applyFilter_method()
	{
		// convert a text to upper case. 
		$input  = 'a text';
		$origin = $input;
		$return = Validator::_applyFilter( $input, 'CenaDTA\Util\Filter::string', 'upper', $error );
		$this->assertTrue( $return );
		$this->assertEquals( strtoupper( $origin ), $input );
		
		// verify a text is all lower case. 
		$input  = 'a text';
		$return = Validator::_applyFilter( $input, 'CenaDTA\Util\Filter::pattern', '[ a-z]*' );
		$this->assertTrue( $return );
		
		// verify a text is not all upper case. and check error message.
		$input  = 'a text';
		$err_msg= 'only upper case';
		$return = Validator::_applyFilter( $input, 'CenaDTA\Util\Filter::pattern', '[A-Z]*', $error, $err_msg );
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
        $found = Validator::_multiple( $source, 'date', $option );
		$this->assertEquals( $correct, $found );
        
        // test input type=date
        $return = Validator::find( $source, 'date', $value, 'date', array(), $error );
		$this->assertEquals( $correct, $value );
		$this->assertTrue( $return );
        
        // test bad input having NULL
        $bad_source = array( 
            'date_y' => '20' . chr(0) . '11',
            'date_m' => '11',
            'date_d' => '15'
        );
        $return = Validator::find( $bad_source, 'date', $value, 'date', array(), $error );
		$this->assertEquals( $correct, $value );
		$this->assertTrue( $return );
	}
	// +----------------------------------------------------------------------+
	public function test_validate_method()
	{
		$input  = 'a text';
		$origin = $input;
		$return = Validator::validate( $input, 'text', array(), $error );
		$this->assertTrue( $return );
		$this->assertEquals( $origin, $input );
		
		$input  = 'a text';
		$origin = $input;
		$return = Validator::validate( $input, 'asis', array(), $error );
		$this->assertTrue( $return );
		$this->assertEquals( $origin, $input );
		
		$input  = "a " . chr(0) . " text";
		$origin = $input;
		$return = Validator::validate( $input, 'asis', array(), $error );
		$this->assertTrue( $return );
		$this->assertNotEquals( $origin, $input );
		$this->assertEquals( str_replace( "\0", '', $origin ), $input );
		
        $input  = ' a test ';
		$origin = $input;
		$return = Validator::validate( $input, 'text', array(), $error );
		$this->assertTrue( $return );
		$this->assertNotEquals( $origin, $input );
		$this->assertEquals( trim( $origin ), $input );
        
		$input  = 'boGus@eXample.com';
		$origin = $input;
		$return = Validator::validate( $input, 'mail', array(), $error );
		$this->assertTrue( $return );
		$this->assertNotEquals( $origin, $input );
		$this->assertEquals( strtolower( $origin ), $input );
		
		$input  = 'a text';
		$origin = $input;
		$return = Validator::validate( $input, 'mail', array(), $error );
		$this->assertFalse( $return );
		
		$input  = '100';
		$return = Validator::validate( $input, 'number', array(), $error );
		$this->assertTrue( $return );
		
		$input  = '１００';
		$origin = $input;
		$return = Validator::validate( $input, 'number', array(), $error );
		$this->assertTrue( $return );
		$this->assertNotEquals( $origin, $input );
		$this->assertEquals( mb_convert_kana( $origin, 'aks', 'UTF-8' ), $input );
		
		$input  = '-100.0';
		$return = Validator::validate( $input, 'number', array(), $error );
		$this->assertFalse( $return );
		
		$input  = '-100.0';
		$return = Validator::validate( $input, 'float', array(), $error );
		$this->assertTrue( $return );
	}
	// +----------------------------------------------------------------------+
	public function test_arrayInput_staff()
	{
        $input = array( '1', ' 2', 'x', '4' );
		$return = Validator::validate( $input, 'number', array(), $error );
		$this->assertFalse( !!$return );
		$this->assertEquals( 'enter a number', $error[2] );
		$this->assertEquals( '2', $input[1] );
	}
	// +----------------------------------------------------------------------+
	public function test_sameas_staff()
	{
		$input    = 'same as';
		$same_str = $input;
		$diff_str = 'diff-rent';
		$return = Validator::_applyFilter( $input, 'CenaDTA\Util\Filter::sameas', $same_str, $error );
		$this->assertTrue( $return );
		
		$return = Validator::_applyFilter( $input, 'CenaDTA\Util\Filter::sameas', $diff_str, $error );
		$this->assertFalse( $return );
		
		$return = Validator::validate( $input, 'asis', array(
				'sameas' => 'same as'
			), $error );
		$this->assertTrue( $return );
		
		$return = Validator::validate( $input, 'asis', array(
			'sameas' => 'different'
		), $error );
		$this->assertFalse( $return );
		$this->assertEquals( Validator::$default_err_msgs[ 'sameas' ], $error );
		
	}
	// +----------------------------------------------------------------------+
}




?>