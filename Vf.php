<?php
namespace CenaDta\Util;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Vf
{
    /**
     * source to search data for. 
     * @var array
     */
    static $source;
    /**
     * found data
     * @var array
     */
    static $data;
    /**
     * error message
     * @var array
     */
    static $error;
    /**
     * number of errors
     * @var integer
     */
    static $err_num;
    // +--------------------------------------------------------------- +
    static function source( $source ) {
        if( !is_null( $source ) && is_array( $source ) ) {
            self::$source = $source;
        }
    }
    // +--------------------------------------------------------------- +
    static function data( $data=NULL ) {
        return self::$data;
    }
    // +--------------------------------------------------------------- +
    static function push( $name, $type='asis', $filter=array() ) {
        $error   = NULL;
        $value   = NULL;
        $success = Validator::find( self::$source, $name, $value, $type, $filter, $error );
        if( !$success ) {
            self::$err_num++;
            self::$error[ $name ] = $error;
        }
        self::$data[ $name ] = $value;
        return $success;
    }
    // +--------------------------------------------------------------- +
}

?>
