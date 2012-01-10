<?php
error_reporting( E_ALL );
require_once( dirname( __FILE__ ) . "/Dispatch.php" );

class Util_DispatchTest extends PHPUnit_Framework_TestCase
{
    var $dispatch;
    // +----------------------------------------------------------------------+
    public function setUp()
    {
        $this->dispatch = new Dispatch();
    }
    // +----------------------------------------------------------------------+
    function test_Dispatch() {
    }
	// +----------------------------------------------------------------------+
}
