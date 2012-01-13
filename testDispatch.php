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
        global $test_dispatch;
        $test_dispatch .= 'default ';
        $ctrl->nextAct( 'done' );
    }
    function actionDone( $ctrl, $data ) {
        global $test_dispatch;
        $test_dispatch .= 'defaultDone ';
    }
}

class chainAuth
{
    function actionDefault( $ctrl, &$data ) {
        $data .= 'defaultAuth ';
    }
    function actionSkip( $ctrl, &$data ) {
        $data .= 'skipAuth ';
        $ctrl->useModel( 'view' );
    }
}

class chainModel
{
    function actionDefault( $ctrl, &$data ) {
        $data .= 'defaultModel ';
    }
    function actionStart( $ctrl, &$data ) {
        $data .= 'startModel ';
        $ctrl->nextModel( 'normal' );
    }
    function actionSkip( $ctrl, &$data ) {
        $data .= 'skipModel ';
    }
}

class chainView
{
    function actionDefault( $ctrl, &$data ) {
        $data .= 'defaultView ';
    }
    function actionNormal( $ctrl, &$data ) {
        $data .= 'normalView ';
    }
    function actionSkip( $ctrl, &$data ) {
        $data .= 'skipView ';
        $ctrl->nextModel( 'normal' );
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
    function test_chainSkip() {
        $chained = 'skip: ';
        $this->dispatch
            ->addModel( 'chainAuth',  'auth' )
            ->addModel( 'chainModel', 'model' )
            ->addModel( 'chainView',  'view' );
        $this->dispatch->dispatch( 'skip', $chained );
        $this->assertEquals( 'skip: skipAuth skipView ', $chained );
    }
    // +----------------------------------------------------------------------+
    function test_chainModel() {
        $chained = 'chain: ';
        $this->dispatch
            ->addModel( 'chainAuth' )
            ->addModel( 'chainModel' )
            ->addModel( 'chainView' );
        $this->dispatch->dispatch( 'start', $chained );
        $this->assertEquals( 'chain: defaultAuth startModel normalView ', $chained );
    }
    // +----------------------------------------------------------------------+
    function test_DefaultModel() {
        global $test_dispatch;
        $model = 'defaultTestModel';
        $this->dispatch->model( $model );

        // check if model is set.
        $check = $this->dispatch->model();
        $this->assertEquals( $model, $check );

        // dispatch simple function: test_func.
        $this->dispatch->dispatch( 'start' );
        $this->assertEquals( 'default defaultDone ', $test_dispatch );
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


