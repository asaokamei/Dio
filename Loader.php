<?php
namespace CenaDta\App;

class Loader
{
    static $location;
    static $postfix = NULL;
    // +-------------------------------------------------------------+
    static function _init() {
    }
    // +-------------------------------------------------------------+
    static function setLocation( $location ) {
        static::$location = $location;
        \Debug::w1( "Loader::setLocation( $location )" );
        return static::$location;
    }
    // +-------------------------------------------------------------+
    function actionDefault( $ctrl, $requests ) {
        \Debug::w1( "Loader::actionDefault(), location=".static::$location );
        // loads from existing app file.
        $action = $requests[0];
        if( self::$postfix === NULL ) {
            $extetion = '.php';
        }
        else {
            $extetion = '_' . self::$postfix . '.php';
        }
        $file_name = static::$location . "/{$action}{$extetion}";
        if( file_exists( $file_name ) ) {
            include( $file_name );
            return;
        }
        $file_name = static::$location . "/{$action}/app{$extetion}";
        if( file_exists( $file_name ) ) {
            include( $file_name );
            return;
        }
        $ctrl->nextAct( 'Err404' );
    }
    // +-------------------------------------------------------------+
    function actionErr404( $ctrl, $data ) {
        // do something about error 404, a file not found.
        \Debug::put( 'Loader::Err404 happened!<br />' );
    }
    // +-------------------------------------------------------------+
}


