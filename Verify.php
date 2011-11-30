<?php
namespace CenaDta\Util;
/**
 * Verify.php 
 * a static class to verify/obtain data from a source. 
 * a wrapper class for Validator class. 
 */
require_once( dirname( __FILE__ ) . "/Validator.php" );

class Verify
{
    /**
     * source to search data for. 
     * @var array
     */
    static $source = array();
    /**
     * found data
     * @var array
     */
    static $data = array();
    /**
     * error message
     * @var array
     */
    static $error = array();
    /**
     * number of errors
     * @var integer
     */
    static $err_num = 0;
    // +--------------------------------------------------------------- +
    static function source( &$source ) {
        if( !is_null( $source ) && is_array( $source ) ) {
            self::$source = &$source;
        }
    }
    // +--------------------------------------------------------------- +
    static function _init() {
        self::$source  = array();
        self::$data    = array();
        self::$error   = array();
        self::$err_num = 0;
    }
    // +--------------------------------------------------------------- +
    static function data( $data=NULL ) {
        return self::$data;
    }
    // +--------------------------------------------------------------- +
    static function verify( $name, $type='asis', $filter=array() ) {
        $error   = NULL;
        $value   = NULL;
        $success = Validator::find( self::$source, $name, $value, $type, $filter, $error );
        if( !$success ) {
            self::$err_num++;
            self::$error[ $name ] = $error;
            return FALSE;
        }
        self::$data[ $name ] = $value;
        return $value;
    }
    // +--------------------------------------------------------------- +
    static function pop( $name ) {
        if( array_key_exists( $name, self::$data ) ) {
            return self::$data[ $name ];
        }
        return FALSE;
    }
    // +--------------------------------------------------------------- +
    static function push( $name, $type ) {
        $args = func_get_args();
        $name = $args[0];
        $type = $args[1];
        
        $args = array_slice( $args, 2 );
        $filter = Util::getArgs( $args, array( 'required', 'pattern', 'default' ) );
        self::verify( $name, $type, $filter );
    }
    // +--------------------------------------------------------------- +
    static function check( $list ) {
        if( is_array( $list ) && !empty( $list ) ) {
            foreach( $list as $name => $item ) {
                self::push( $name, $item[0], $item[1] );
            }
        }
        return isError();
    }
    // +--------------------------------------------------------------- +
    static function isError( &$error=NULL ) {
        $error = self::$error;
        return !self::$err_num;
    }
    // +--------------------------------------------------------------- +
}

?>
