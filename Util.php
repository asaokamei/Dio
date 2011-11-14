<?php
namespace CenaDta\Dio;

class Util
{
	// +--------------------------------------------------------------- +
	/** checks if a variable ($var) have a value (non empty value).
	 *  returns TRUE if $var==0 as well.
	 */
	static function isValue( $var, $name=FALSE ) {
		if( is_array( $var ) ) { 
			if( $name === FALSE ) {
				return( count( $var ) ); 
			}
			if( isset( $var[ $name ] ) ) {
				return have_value( $var[ $name ] );
			}
			return FALSE;
		}
		else
		if( is_object( $var ) ) {
			return TRUE;
		}
		else
		if( "$var" == "" ) { 
			return FALSE; 
		}
		else { 
			return TRUE; 
		}
	}
	// +--------------------------------------------------------------- +
	/** gets a value in $arr[ $name ] without causing E_NOTICE error.
	 */
	static function getValue( $arr, $name, $default=FALSE ) {
		if( isset( $arr[ $name ] ) ) {
			return $arr[ $name ];
		}
		return $default;
	}
}

?>