<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use CenaDTA\Util\Util as Util;
// in this test, error_reporting is set to off to test void staff.
error_reporting( E_ALL );
require_once( dirname( __FILE__ ) . "/Util.php" );
define( 'WORDY', 0 );

/**
 * PHPUnit test for Web Input/Output object.
 */
class Util_UtilTest extends PHPUnit_Framework_TestCase
{
	// +----------------------------------------------------------------------+
	public function setUp()
	{
	}
	// +----------------------------------------------------------------------+
	public function test_isValue()
	{
        // test just simple value. 
        $this->assertTrue(  Util::isValue( 'A' ) );
        $this->assertTrue(  Util::isValue( ' ' ) );
        $this->assertFalse( Util::isValue( '' ) );
        $this->assertFalse( Util::isValue( FALSE ) );
        $this->assertFalse( Util::isValue( NULL ) );
        error_reporting( E_ALL ^ E_NOTICE );
        $this->assertFalse( Util::isValue( $void ) );
        error_reporting( E_ALL );
        
        // test a value in an array.
        $data = array( 'test' => 'test' );
        $this->assertTrue(  Util::isValue( $data ) );
        $this->assertTrue(  Util::isValue( $data, 'test' ) );
        $this->assertFalse( Util::isValue( $data, 'none' ) );
        
        // case if a value is a string.
        $data = 'some_string_which_can_be_a_array';
        $this->assertTrue(  Util::isValue( $data ) );
        $this->assertFalse( Util::isValue( $data, 'test' ) );
        error_reporting( E_ALL ^ E_NOTICE );
        $this->assertFalse( Util::isValue( $void, 'test' ) );
        error_reporting( E_ALL );
        
        // test a value in an array.
        $data = array( 'test' => 'test' );
        $this->assertTrue(  Util::isValue( $data, 'test' ) );
        
        $data = array( 'test' => '' );
        $this->assertFalse(  Util::isValue( $data, 'test' ) );
        
        $data = array( 'test' => FALSE );
        $this->assertFalse(  Util::isValue( $data, 'test' ) );
        
        $data = array( 'test' => NULL );
        $this->assertFalse(  Util::isValue( $data, 'test' ) );
        
	}
	// +----------------------------------------------------------------------+
}


?>
