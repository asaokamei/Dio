<?php
/**
 * Dispatcher for application controller.
 * uses Chain of Responsibility pattern...
 */

class Dispatch
{
    var $model   = NULL;
    var $nextAct = NULL;
    var $currAct = NULL;
    var $defaultAct = 'default';
    var $hookBefore = FALSE;
    var $hookAfter  = FALSE;
    // +-------------------------------------------------------------+
    function model( $model=NULL ) {
        if( $model !== NULL ) {
            $this->model = $model;
        }
        return $this->model;
    }
    // +-------------------------------------------------------------+
    function nextAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->nextAct = $action;
        }
        return $this->nextAct;
    }
    // +-------------------------------------------------------------+
    function currAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->currAct = $action;
        }
        return $this->currAct;
    }
    // +-------------------------------------------------------------+
    function defaultAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->defaultAct = $action;
        }
        return $this->defaultAct;
    }
    // +-------------------------------------------------------------+
    function hookBefore( $action=NULL ) {
        if( $action !== NULL ) {
            $this->hookBefore = $action;
        }
        return $this->hookBefore;
    }
    // +-------------------------------------------------------------+
    function hookAfter( $action=NULL ) {
        if( $action !== NULL ) {
            $this->hookAfter = $action;
        }
        return $this->hookAfter;
    }
    // +-------------------------------------------------------------+
    function dispatch( $action, $data=NULL ) {
        // set current action.
        $this->currAct( $action );
        // -----------------------------
        // do the hook before action.
        $exec = $this->getExecFromAction( $this->hookBefore );
        if( $exec ) {
            $this->execute( $this->hookBefore, $data );
        }
        // -----------------------------
        // chain of responsibility loop.
        $return = NULL;
        while( $action ) {
            $this->nextAct( FALSE );
            $return = $this->execAction( $action, $data );
            if( $return === FALSE ) exit;
            $action = $this->nextAct();
            $this->currAct( $action );
        }
        // -----------------------------
        // do the hook after action.
        $exec = $this->getExecFromAction( $this->hookAfter );
        if( $exec ) {
            $this->execute( $this->hookAfter, $data );
        }
        return $return;
    }
    // +-------------------------------------------------------------+
    function execute( $exec, $data=NULL ) {
        return call_user_func( $exec, array( $this, $data ) );
    }
    // +-------------------------------------------------------------+
    function getExecFromAction( $action ) {
        $exec = FALSE;
        if( !$action ) return $exec;
        if( isset( $this->model ) && is_callable( array( $this->model, $action ) ) ) {
            $exec = array( $this->model, $action );
        }
        else
        if( is_callable( $action ) ) {
            $exec = $action;
        }
        return $exec;
    }
    // +-------------------------------------------------------------+
    function execAction( $action, $data=NULL ) {
        $exec = $this->getExecFromAction( $action );
        if( !$exec ) {
            $exec = $this->getExecFromAction( $this->defaultAct );
        }
        if( isset( $exec ) ) {
            return $this->execute( $exec, $data );
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}

/*
class sample_dispatch
{
    function someAction( $ctrl, $data ) {
        $ctrl->nextAct( 'moreAction' );
    }
    function moreAction( $ctrl,$data ) {
        $ctrl->nextAct( 'done' );
    }
}

$data = array( 'test' => 'test' );
$ctrl = new Dispatch();
$ctrl->setModel( 'sample_dispatch' );
$ctrl->dispatch( 'someAction', $data );

*/

