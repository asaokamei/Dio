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
        if( !empty( $requests ) ) {
            $this->requests = $requests;
        }
        else {
            $this->requests = $this->getRoute();
        }
    }
    // +-------------------------------------------------------------+
    /**
     * @param null $uri
     * @param null $script
     * @return array     returns routes.
     */
    function getRoute( $uri=NULL, $script=NULL ) {
        Debug::w1( "getRoute( $uri, $script )" );
        if( $uri === NULL ) {
            $uri = preg_replace('@[\/]{2,}@', '/', $_SERVER[ 'REQUEST_URI' ] );
            $uri = explode( '/', $uri );
        }
        if( $script === NULL ) {
            $script = explode( '/', $_SERVER[ 'SCRIPT_NAME' ] );
        }
        Debug::t5( $uri, 'uri array' );
        Debug::t5( $script, 'script array' );
        for( $i = 0; $i < sizeof( $script ); $i++ ) {
            if( $uri[$i] == $script[$i] ) {
                unset( $uri[$i] );
            }
        }
        return array_values( $uri );
    }
    // +-------------------------------------------------------------+
    function dispatch( $dispatcher, &$data ) {
        $dispatcher->dispatch( $this->requests[0], $data );
    }
    // +-------------------------------------------------------------+
}


