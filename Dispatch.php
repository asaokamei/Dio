<?php
/**
 * Dispatcher for application controller.
 * uses Chain of Responsibility pattern...
 * TODO: even if cache is hit, models and views are loaded.
 */

class Dispatch
{
    // ---------------------------------
    /**
     * @var null    object model.
     */
    var $model   = NULL;
    /**
     * @var null    current model name.
     */
    var $modelName = NULL;
    /**
     * @var array   list of models. nextModel sets model to the next.
     */
    var $models  = array();

    // ---------------------------------
    /**
     * @var null    name of next action.
     */
    var $nextAct = NULL;
    /**
     * @var null    name of current action.
     */
    var $currAct = NULL;
    /**
     * @var null    name of original dispatched action.
     */
    var $dispatchAct= NULL;
    /**
     * @var string   default exec name if not matched.
     */
    var $defaultAct = 'default';
    /**
     * @var string   prefix for action to func/method name.
     */
    var $preAction = 'action';
    // +-------------------------------------------------------------+
    function __construct() {
        // nothing.
    }
    // +-------------------------------------------------------------+
    /**
     * set/get model.
     * @param null $model
     *     specify model to use. the second model specified is stored
     *     in models array, and can be used by nextModel.
     * @param $name   name of model.
     * @return mix    returns current model.
     */
    function model( $model=NULL, $name=NULL ) {
        if( $model !== NULL ) {
            $this->addModel( $model, $name );
        }
        return $this->model;
    }
    // +-------------------------------------------------------------+
    /**
     * @return string     returns current model name.
     */
    function modelName() {
        return $this->modelName;
    }
    // +-------------------------------------------------------------+
    /**
     * adds models to Dispatcher.
     * the first model is set to $this->model, the subsequent ones
     * are stored in $this->models[].
     * @param $model      model class or object.
     * @param $name       name of model.
     * @return Dispatch   returns this.
     */
    function addModel( $model, $name=NULL ) {
        if( is_null( $this->model ) ) {
            $this->model = $model;
            $this->modelName = $name;
        }
        else {
            $this->models[] = array( $model, $name );
        }
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     * use next model. for instance, the models can be: auth,
     * cache, data model, and view.
     * @param null $nextAct
     *     sets action name to start the next model. if not set,
     *     uses current action.
     * @return bool/string    next action if next model exists. FALSE if not.
     */
    function nextModel( $nextAct=NULL ) {
        if( isset( $this->models[0] ) ) {
            // replace model with the next model.
            $this->model  = $this->models[0][0];
            $this->modelName = $this->models[0][1];
            $this->models = array_slice( $this->models, 1 );
            // sets next action for the next model.
            if( $nextAct === NULL ) {
                $nextAct = $this->nextAct( $this->currAct() );
            }
            else {
                $this->nextAct( $nextAct );
            }
            return $nextAct;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * set current model to given name.
     * @param $name           name of model to set.
     * @param null $action    next action if any.
     * @return bool|string    returns model name set, or false if not found.
     */
    function useModel( $name ) {
        while( $this->models ) {
            if( $name === $this->models[0][1] ) {
                return $name;
            }
            else {
                $this->models = array_slice( $this->models, 1 );
            }
        }
        // should throw an exception, maybe.
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * @return bool   TRUE if more models exists.
     */
    function moreModels() {
        return !empty( $this->models );
    }
    // +-------------------------------------------------------------+
    /**
     * use next model. for instance, the models can be: auth,
     * cache, data model, and view.
        }
        throw new RuntimeException( 'no next model in Dispatch. ' );
    }
    // +-------------------------------------------------------------+
    /**
     * set/get next action.
     * @param null $action   sets next action if set.
     * @return string        returns next action.
     */
    function nextAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->nextAct = $action;
        }
        return $this->nextAct;
    }
    // +-------------------------------------------------------------+
    /**
     * set/get current action.
     * @param null $action    sets current action if set.
     * @return string         returns current action.
     */
    function currAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->currAct = $action;
        }
        return $this->currAct;
    }
    // +-------------------------------------------------------------+
    /**
     * @param null $action
     * @return string
     */
    function defaultAct( $action=NULL ) {
        if( $action !== NULL ) {
            $this->defaultAct = $action;
        }
        return $this->defaultAct;
    }
    // +-------------------------------------------------------------+
    /**
     * starts loop. I think this is chain of responsibility pattern.
     * @param $action           name of action to start.
     * @param null $data        data to pass to each exec method.
     * @return bool|mixed|null  returns the last returned value.
     */
    function dispatch( $action, &$data=NULL )
    {
        // set current action.
        $return = NULL;
        $this->dispatchAct = $action;
        $this->currAct( $action );
        // -----------------------------
        // chain of responsibility loop.
        while( $action  )
        {
            $this->nextAct( FALSE ); // reset next action.
            $return = $this->execAction( $action, $data );
            $action = $this->nextAct();
            // automatically advance to next model.
            if( !$action &&  // next action not set
                $this->moreModels() ) { // still model exists
                $action = $this->nextModel(); // advance model using current action
            }
        }
        // -----------------------------
        return $return;
    }
    // +-------------------------------------------------------------+
    /**
     * executes the function/method.
     * @param callback $exec  callable object (function or obj/method).
     * @param null $data      data to pass if any.
     * @return bool|mixed     returned value from exec object.
     */
    function execute( $exec, &$data=NULL ) {
        $return = call_user_func_array( $exec, array( $this, &$data ) );
        if( $return === FALSE ) exit;
        return $return;
    }
    // +-------------------------------------------------------------+
    /**
     * get exec object from action name.
     * either it is a model/method or function.
     * @param string $action   name of action.
     * @return array|bool      found exec object.
     */
    function getExecFromAction( $action ) {
        $exec = FALSE;
        if( !$action ) return $exec;
        if( $this->preAction ) {
            $action = $this->preAction . ucwords( $action );
        }
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
    /**
     * execute action based on action name and default.
     * @param $action      name of action to execute.
     * @param null $data   data to pass if any.
     * @return bool|mixed  returned value from exec object.
     */
    function execAction( $action, &$data=NULL ) {
        $exec = $this->getExecFromAction( $action );
        if( !$exec ) {
            $exec = $this->getExecFromAction( $this->defaultAct );
        }
        if( $exec ) {
            return $this->execute( $exec, $data );
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
}

/*

class auth {
    function default( $ctrl, $data ) {
        if( $login ) {
            $ctrl->nextModel();
        }
        else {
            $ctrl->nextModel( 'loginForm' );
        }
    }
}

class cache {
    function user( $ctrl, $data ) {
        // check if user html is in cache.
        // show cached html, and
        return FALSE;
    }
    function LoginForm( $ctrl, $data ) {
        // check if login form is in cache.
        // show cached html, and
        return FALSE;
    }
}

class model {
    function user( $ctrl, $data ) {
    }
    function LoginForm( $ctrl, $data ) {
        // do nothing.
    }
}

class view {
    function user( $ctrl, $data ) {
    }
    function loginForm( $ctrl, $data ) {
        // show login form.
    }
}

$ctrl = new Dispatch();
    ->addModel( 'auth' )
    ->addModel( 'cache' )
    ->addModel( 'model' )
    ->addModel( 'view' )
    ->dispatch( 'user' );

*/
/*
class sample_dispatch
{
    function startAction( $ctrl, $data ) {
        $ctrl->nextAct( 'moreAction' );
    }
    function moreAction( $ctrl,$data ) {
        $ctrl->nextAct( 'done' );
    }
}

function done( $ctrl, $data ) {
    $ctrl->nextModel( 'finalAct' );
}

class more_dispatch
{
    function finalAct( $ctrl, $data ) {
    }
}

$data = array( 'test' => 'test' );
$ctrl = new Dispatch();
$ctrl->model( 'sample_dispatch' );
$ctrl->model( 'more_dispatch' );
$ctrl->dispatch( 'startAction', $data );

*/

