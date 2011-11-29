<?php
namespace CenaDTA\Util;

class Util
{
	// +--------------------------------------------------------------- +
	/** checks if a variable ($var) have a value (non empty value).
	 *  returns TRUE if $var==0 as well.
	 */
	static function isValue( $var, $name=FALSE ) {
        if( $name !== FALSE ) {
            if( !is_array( $var ) ) {
                return FALSE;
            }
			if( isset( $var[ $name ] ) ) {
				return self::isValue( $var[ $name ] );
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
        if( is_array( $arr ) && isset( $arr[ $name ] ) ) {
			return $arr[ $name ];
		}
		return $default;
	}
	// +--------------------------------------------------------------- +
    static function arrayWalk( &$value, $func ) {
        if(is_array( $value ) ) {
            return array_walk_recursive( $value, $func );
        }
        return $func( $value, NULL );
    }
	// +--------------------------------------------------------------- +
}

?>