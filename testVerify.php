<?php
use CenaDTA\Util\Verify as Verify;
error_reporting( E_ALL );
require_once( dirname( __FILE__ ) . "/Verify.php" );
define( 'WORDY', 0 );

/**
 * PHPUnit test for Data Input/Output object.
 */
class Util_VerifyTest extends PHPUnit_Framework_TestCase
{
	// +----------------------------------------------------------------------+
	public function setUp()
	{
        Verify::_init();
	}
	// +----------------------------------------------------------------------+
	public function test_test()
	{
	}
	// +----------------------------------------------------------------------+
	public function test_ArrayKeyExists()
	{
        $data = array( 
            'test' => NULL
        );
        $this->assertTrue( array_key_exists( 'test', $data ) );
        $this->assertFalse( array_key_exists( 'test2', $data ) );
	}
	// +----------------------------------------------------------------------+
	public function test_SimplePush()
	{
        // simply find a value.
        $name = 'test';
        $value = 'test value';
        $data = array(
            $name => $value,
        );
        Verify::source( $data );
        
        // push $name into Verify.
        $found = Verify::push( $name );
		$this->assertEquals( $value, $found );
        $popped = Verify::pop( $name );
		$this->assertEquals( $value, $popped );
        
        // push non-existent data. 
        $bad_name = 'not_exist';
        $found = Verify::push( $bad_name );
		$this->assertTrue( is_null( $found ) );
        $popped = Verify::pop( $bad_name );
		$this->assertTrue( is_null( $popped ) );
        
        // push non-existent data and create an error.
        $bad_name = 'not_exist';
        $found = Verify::push( $bad_name, 'asis', array( 'required'=>TRUE ) );
		$this->assertFalse( $found );
        $popped = Verify::pop( $bad_name );
		$this->assertTrue( is_null( $popped ) );
    }
}

?>
