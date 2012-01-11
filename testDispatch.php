<?php
error_reporting( E_ALL );
require_once( dirname( __FILE__ ) . "/Dispatch.php" );

function actionTestFunc( $ctrl, $data ) {
    global $test_dispatch;
    $test_dispatch .= 'functionTest';
}

class oneModel
{
    function actionStart( $ctrl, $data ) {
        global $test_dispatch;
        $test_dispatch .= 'oneStart ';
        $ctrl->nextAct( 'more' );
    }
    function actionMore( $ctrl, $data ) {
        global $test_dispatch;
        $test_dispatch .= 'oneMore ';
        $ctrl->nextAct( 'done' );
    }
    function actionDone( $ctrl, $data ) {
        global $test_dispatch;
        $test_dispatch .= 'oneDone ';
    }
}

class defaultTestModel
{
    function actionDefault( $ctrl ) {

    }
}

class Util_DispatchTest extends PHPUnit_Framework_TestCase
{
    var $dispatch;
    // +----------------------------------------------------------------------+
    public function setUp()
    {
        global $test_dispatch;
        $test_dispatch  = FALSE;
        $this->dispatch = new Dispatch();
    }
    // +----------------------------------------------------------------------+
    function test_SingleModel() {
        global $test_dispatch;
        $model = 'oneModel';
        $this->dispatch->model( $model );

        // check if model is set.
        $check = $this->dispatch->model();
        $this->assertEquals( $model, $check );

        // dispatch simple function: test_func.
        $this->dispatch->dispatch( 'start' );
        $this->assertEquals( 'oneStart oneMore oneDone ', $test_dispatch );
    }
    // +----------------------------------------------------------------------+
    function test_Dispatch() {
        global $test_dispatch;
        $model = 'test';
        $this->dispatch->model( $model );

        // check if model is set.
        $check = $this->dispatch->model();
        $this->assertEquals( $model, $check );

        // dispatch simple function: test_func.
        $this->dispatch->dispatch( 'testFunc' );
        $this->assertEquals( 'functionTest', $test_dispatch );
    }
	// +----------------------------------------------------------------------+
}


