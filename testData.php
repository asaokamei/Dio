<?php
use CenaDTA\Util\Data as DataIO;
error_reporting( E_ALL );
require_once( dirname( __FILE__ ) . "/Data.php" );
require_once( dirname( __FILE__ ) . "/Validator.php" );
define( 'WORDY', 0 );

/**
 * PHPUnit test for Data Input/Output object.
 */
class Util_DataIOTest extends PHPUnit_Framework_TestCase
{
	// +----------------------------------------------------------------------+
	public function setUp()
	{
	}
	// +----------------------------------------------------------------------+
	public function test_Data()
	{
        $dio = new CenaDTA\Util\Data();
        // let's find some value. 
        $name = 'test';
        $test = 'some value';
        $_POST[ $name ] = $test;
        $return = $dio->find( $value, $name );
        $this->assertTrue( $return !== FALSE );
        $this->assertEquals( $test, $value );
        
        // let's find date, which is in several fields.
        $name = 'testdate';
        $date = '2011-11-30';
        $_POST[ "{$name}_y" ] = '2011';
        $_POST[ "{$name}_m" ] = '11';
        $_POST[ "{$name}_d" ] = '30';
        $return = $dio->find( $value, $name, 'date' );
        $this->assertTrue( $return !== FALSE );
        $this->assertEquals( $date, $value );
	}
	// +----------------------------------------------------------------------+
	public function test_PushMethod()
	{
        $dio = new CenaDTA\Util\Data();
        // let's find some value. 
        $nametest = 'test';
        $val_test = 'some value';
        $_POST[ $nametest ] = $val_test;
        
        $namedate = 'testdate';
        $val_date = '2011-11-30';
        $_POST[ "{$namedate}_y" ] = '2011';
        $_POST[ "{$namedate}_m" ] = '11';
        $_POST[ "{$namedate}_d" ] = '30';
        
        $dio->push( $nametest );
        $dio->push( $namedate, 'date' );
        $this->assertEquals( $val_test, $dio->pop( $nametest ) );
        $this->assertEquals( $val_date, $dio->pop( $namedate ) );
	}
	// +----------------------------------------------------------------------+
	public function test_ErrroMethods()
	{
        $dio = new CenaDTA\Util\Data();
        // let's find some value. 
        $nametest = 'test';
        $val_test = 'some value';
        $_POST[ $nametest ] = $val_test;
        $dio->push( $nametest, 'date' ); // wrong type of data.
        $success = $dio->popError( $err_msg );
        $this->assertEquals( $val_test, $dio->pop( $nametest ) );
        $this->assertTrue( $success );
	}
	// +----------------------------------------------------------------------+
}
?>
