<?php
namespace CenaDta\App;

/**
 * dumb/simple application loader.
 * currently, it searches app.php or action.php is subsequent folders.
 * this maybe slow.
 * TODO: use route map for finding applications.
 */
class Loader
{
    static $location;
    static $postfix = NULL;
    static $prefix  = '.';
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
    /**
     * loads application based on folder structure.
     * say, uri is 'action/action2/...', this loader looks for
     * app.php, action.php, first. if not found, searches for
     * action/app.php, then action/action2.php.
     * @param $ctrl
     * @param $requests
     * @return bool        TRUE if app found, FALSE if not found.
     */
    function actionDefault( $ctrl, &$requests ) {
        \Debug::w1( "Loader::actionDefault(), location=".static::$location );
        // loads from existing app file.
        $action = $requests[0];
        if( self::$postfix === NULL ) {
            $extension = '.php';
        }
        else {
            $extension = '_' . self::$postfix . '.php';
        }
        $prefix = self::$prefix;

        // load application.

        // try loading action/app.php
        $file_name = static::$location . "/{$prefix}app{$extension}";
        if( file_exists( $file_name ) ) {
            include( $file_name );
            return TRUE;
        }
        // try loading action.php script.
        $file_name = static::$location . "/{$action}{$extension}";
        if( file_exists( $file_name ) ) {
            include( $file_name );
            return TRUE;
        }
        // try load in subsequent folder.
        $folder = static::$location . "/{$action}";
        if( is_dir( $folder ) ) {
            $sub_req = array_slice( $requests, 1 );
            return self::actionDefault( $ctrl, $sub_req );
        }
        $ctrl->nextAct( 'Err404' );
        return FALSE;
    }
    // +-------------------------------------------------------------+
    function actionErr404( $ctrl, $data ) {
        // do something about error 404, a file not found.
        \Debug::put( 'Loader::Err404 happened!<br />' );
    }
    // +-------------------------------------------------------------+
}


