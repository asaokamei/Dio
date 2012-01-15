<?php
require_once( '../Debug.php' );
require_once( '../Loader.php' );
require_once( '../Viewer.php' );
require_once( '../Router.php' );
require_once( '../Dispatch.php' );

/**
 * TODO: make new Loader
 *  by inheriting Dispatch.php,
 *  add routing function to initiate the main loop,
 *  and add data for controlling a web site, such as
 *  debug, admin, bread, etc.
 * TODO: change Loader as Router,
 *  and merge Router into the above new Loader.
 * TODO: make Config
 *  for controlling multiple-lang, admin, debug info,
 *  dev/staging/real server.
 * TODO: Loader needs various loading method.
 *  app.php: load php and prepend model. not sure if
 *           loader/app.php to prepend...
 *  action.php: include php and intercept output by ob.
 *  action.html: read html as a contents.
 *  action is a file: output as is and set new mime type.
 * TODO: demo as test site.
 *  before eating dog food, make test/demo site.
 * TODO: Debug is... awful. 
 */
Debug::w1( 'index.php is here' );

class Loader extends CenaDta\App\Loader {}
Loader::setLocation( __DIR__ );
class Viewer extends CenaDta\App\Viewer {}

$data = array();
$dispatch = new Dispatch();
$dispatch
    ->addModel( 'Loader', 'load' )
    ->addModel( 'Viewer', 'view' )
;

$dispatch->dispatch( $dispatch->defaultAct(), $data );



