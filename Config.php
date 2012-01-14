<?php
namespace CenaDta\App;

class Config
{
    static $loaded = FALSE;
    static $prefix = '.';
    static $postfix = NULL;
    static $folder  = 'Config';
    static $appRoot = NULL;
    static $currFolder = NULL;
    // +-------------------------------------------------------------+
    static function _init() {
    }
    // +-------------------------------------------------------------+
    function actionDefault( $ctrl, $request ) {

    }
    // +-------------------------------------------------------------+
}