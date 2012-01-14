<?php
namespace CenaDta\App;
/**
 * dumb/simple router to dispatch application.
 */

class Router
{
    // +-------------------------------------------------------------+
    /**
     * @param null $uri
     * @param null $script
     * @return array     returns routes.
     */
    static function getRoute( $uri=NULL, $script=NULL ) {
        \Debug::w1( "getRoute( $uri, $script )" );
        if( $uri === NULL ) {
            $uri = preg_replace('@[\/]{2,}@', '/', $_SERVER[ 'REQUEST_URI' ] );
            $uri = explode( '/', $uri );
        }
        if( $script === NULL ) {
            $script = explode( '/', $_SERVER[ 'SCRIPT_NAME' ] );
        }
        \Debug::t5( $uri, 'uri array' );
        \Debug::t5( $script, 'script array' );
        for( $i = 0; $i < sizeof( $script ); $i++ ) {
            if( $uri[$i] == $script[$i] ) {
                unset( $uri[$i] );
            }
        }
        return array_values( $uri );
    }
    // +-------------------------------------------------------------+
}


