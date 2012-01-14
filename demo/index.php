<?php
require_once( '../Debug.php' );
require_once( '../Loader.php' );
require_once( '../Viewer.php' );
require_once( '../Router.php' );
require_once( '../Dispatch.php' );

Debug::w1( 'index.php is here' );

class Loader extends CenaDta\App\Loader {}
Loader::setLocation( __DIR__ );
class Viewer extends CenaDta\App\Viewer {}

$dispatch = new Dispatch();
$dispatch
    ->addModel( 'Loader', 'load' )
    ->addModel( 'Viewer', 'view' )
;

$route = new Router();
Debug::t1( $route->requests );

$route->dispatch( $dispatch );


