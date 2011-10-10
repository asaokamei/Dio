<?php
namespace CenaDta\Dio;

class Verify
{
	static $filter_classes = array( 'CenaDta\Dio\Verify' );
	// +--------------------------------------------------------------- +
	// BASIC METHODS 
	// +--------------------------------------------------------------- +
	/** main verify method for verifying value using this Verify class. 
	 */ 
	function verify( $value, &$error, $verify_name, $options ) {
		$success = TRUE;
		if( !have_value( $value ) ) {     // do not verify if empty. 
			return $success;
		}
		else 
		if( is_array( $value ) ) {        // do recursive verify on $val. 
			if( !is_array( $error ) ) { // make sure $error is an array. 
				$error = array(); 
			}
			foreach( $value as $key => $val ) {
				$success &= self::verify( $val, $error[$key], $verify_name, $options );
			}
			return $success;
		}
		// verify value. 
		foreach( self::$verify_classes as $class ) {
			if( method_exists( $class, $verify_name ) ) {
				$success = $class::$verify_name( $value, $options );
			}
		}
		if( function_exists( $filter_name ) ) {
			$success = $filter_name( $value, $options );
		}
		if( !$success ) { // it's an error. set an error message in $error.
			if( isset( $options[ 'err_msg' ] ) ) {
				$error = $options[ 'err_msg' ];
			}
			else {
				$error = $verify_name;
			}
			if( WORDY ) echo "<font color=red>verify failed( $value, $error, $verify_name, $options );</font><br/>\n";
		}
		return $success;
	}
	// +--------------------------------------------------------------- +
	/** add another class for filtering, such as FilterJa. 
	 */
	function setFilterClass( $class ) {
		self::$filter_classes[] = $class;
	}
	// +--------------------------------------------------------------- +
	// VERIFY METHODS!!!
	// +--------------------------------------------------------------- +
	function exist( $val, $option ) {
		if( $option ) return have_value( $val );
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	function regexp( $val, $option ) {
		if( have_value( $val ) ) {
			return preg_match( "/$option/", $val );
		}
		return TRUE;
	}
	// +--------------------------------------------------------------- +
}


?>