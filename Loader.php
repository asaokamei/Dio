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
    static function actionDefault( $ctrl, &$requests ) {
        // load by searching routes.
        $routes = self::getRoute();
        $file_name = self::searchRoutes( $routes );
        if( $file_name ) {
            include $file_name;
        }
        else {
            $ctrl->nextAct( 'Err404' );
        }
    }
    // +-------------------------------------------------------------+
    function actionErr404( $ctrl, $data ) {
        // do something about error 404, a file not found.
        \Debug::put( 'Loader::Err404 happened!<br />' );
    }
    // +-------------------------------------------------------------+
    /**
     * loads application based on folder structure.
     * say, uri is 'action/action2/...', this loader looks for
     * app.php, action.php, first. if not found, searches for
     * action/app.php, then action/action2.php.
     * @param $routes                   route to search for.
     * @return bool|string $file_name   search file name, or FALSE if not found..
     */
    static function searchRoutes( &$routes ) {
        // loads from existing app file.
        $action = $routes[0];
        if( self::$postfix === NULL ) {
            $extension = '.php';
        }
        else {
            $extension = '_' . self::$postfix . '.php';
        }
        $prefix = self::$prefix;
        \Debug::w1( "Loader::searchRoutes(), action={$action}, location=".static::$location );

        // load application.

        // try loading action.php script.
        $file_name = static::$location . "/{$action}{$extension}";
        if( file_exists( $file_name ) ) {
            $routes = array_slice( $routes, 1 );
            return $file_name;
        }
        // try load in subsequent action folder.
        $folder = static::$location . "/{$action}";
        if( is_dir( $folder ) && !empty( $routes ) ) {
            $routes = array_slice( $routes, 1 );
            self::$location = $folder;
            return self::searchRoutes( $routes );
        }
        // try loading ./app.php
        $file_name = static::$location . "/{$prefix}app{$extension}";
        if( file_exists( $file_name ) ) {
            return $file_name;
        }
        return FALSE;
    }
    // +-------------------------------------------------------------+
    /**
     * gets routes array. override this method to use other Router.
     * @static
     * @return array    routes.
     */
    static function getRoute() {
        return Router::getRoute();
    }
    // +-------------------------------------------------------------+
    /**
     * search maps to find file to load.
     * this should be much faster than searchRoutes because there
     * are no file system access.
     * not implemented!!!
     * @static
     * @param $routes
     */
    static function searchMaps( $routes ) {
        // not implemented.
    }
    // +-------------------------------------------------------------+
}


