<?php
/**
 * dumb/simple router to dispatch application.
 */

class Router
{
    /**
     * @var array   holds uri divided by /
     */
    var $requests = array();
    // +-------------------------------------------------------------+
    /**
     * constructor sets routes from URI if routes not set.
     * @param array $requests   set routes.
     */
    function __construct( $requests=array() ) {
        if( empty( $requests ) ) {
            $this->requests = $requests;
        }
        else {
            $this->requests = $this->routeUri();
        }
    }
    // +-------------------------------------------------------------+
    /**
     * @return array     returns routes.
     */
    function routeUri( $uri=NULL, $script=NULL ) {
        if( $uri === NULL ) {
            $uri = preg_replace('@[\/]{2,}@', '/', $_SERVER[ 'REQUEST_URI' ] );
            $uri = explode( '/', $uri );
        }
        if( $script === NULL ) {
            $script = explode( '/', $_SERVER[ 'SCRIPT_NAME' ] );
        }
        for( $i= 0; $i < sizeof( $script ); $i++ ) {
            if( $uri[$i] == $script[$i] ) {
                array_slice( $uri, 1 );
            }
        }
        return $uri;
    }
    // +-------------------------------------------------------------+
    function dispatch( $dispatcher ) {
        $dispatcher->dispatch( $this->requests[0], $this->requests );
    }
    // +-------------------------------------------------------------+
}


class simpleLoader
{
    static $location;
    static $postfix = '';
    // +-------------------------------------------------------------+
    function actionDefault( $ctrl, $requests ) {
        // loads from existing app file.
        $action = $requests[0];
        $extetion = '_' . self::$postfix . '.php';
        if( file_exists( "{$action}.php" ) ) {
            include( "{$action}{$extetion}" );
        }
        else
        if( file_exists( "{$action}/app.php" ) ) {
            include( "{$action}/app{$extetion}" );
        }
        $ctrl->nextAct( 'Err404' );
    }
    // +-------------------------------------------------------------+
    function actionErr404( $ctrl, $data ) {
        // do something about error 404, a file not found.
    }
    // +-------------------------------------------------------------+
}

class simpleView
{
    function actionDefault( $ctrl, $data ) {
        // everything OK.
    }
    function actionErr404( $ctrl, $data ) {
        // show some excuses, or blame user for not finding a page.
    }
    function actionException( $ctrl, $data ) {
        // show some nasty things happened and apologize.
    }
}

