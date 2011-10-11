<?php
namespace CenaDta\Dio;

class Filter
{
	// +--------------------------------------------------------------- +
	// FILTER METHODS!!!
	// +--------------------------------------------------------------- +
	function trim( &$value, $option=array() ) {
		$value = trim( $value );
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	function string( &$value, $option=array() ) {
		if( $option == 'lower' ) {
			$value = strtolower( $value )
		}
		else
		if( $option == 'upper' ) {
			$value = strtoupper( $value )
		}
		else
		if( $option == 'capital' ) {
			$value = strcapital( $value )
		}
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	function sanitizeEmail( &$value, $option=array() ) {
		$value = self::sanitize( $value, FILTER_SANITIZE_EMAIL, $option )
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	function sanitize( &$value, $option=array() ) {
		if( !filter_var( $value, $filter, $option  ) ) {
			$value = '';
		}
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	// VERIFIERS METHODS!!!
	// +--------------------------------------------------------------- +
	function code( $val, $option ) {
		return self::regexp( $val, '[-_0-9a-zA-Z]*' );
	}
	// +--------------------------------------------------------------- +
	function length( $val, $option ) {
		$ok  = TRUE;
		$len = strlen( $val );
		if( is_array( $option ) ) {
			if( isset( $option[ 'min' ] ) && $len < $option[ 'min' ] ) {
				$ok = FALSE;
			}
			if( isset( $option[ 'max' ] ) && $len > $option[ 'max' ] ) {
				$ok = FALSE;
			}
		}
		else {
			if( $len != $option ) {
				$ok = FALSE;
			}
		}
		return $ok;
	}
	// +--------------------------------------------------------------- +
	function regexp( $val, $option ) {
		return preg_match( "/$option/", $val );
	}
	// +--------------------------------------------------------------- +
	// ERROR MESSAGES
	// +--------------------------------------------------------------- +
	function err_msg( $filter, $option ) 
	{
		$err_msg = FALSE;
		switch( $filter ) {
			case 'required':
				$err_msg = 'required field';
				break;
			case 'length':
				if( !is_array( $option ) ) {
					$err_msg = "length is {$option} charactor";
				}
				else {
					$err_msg = "length must be: ";
					if( isset( $option[ 'min' ] ) ) {
						$err_msg = " longer than {$option{'min'}}";
					}
					if( isset( $option[ 'max' ] ) ) {
						$err_msg = " shorter than {$option{'max'}}";
					}
				}
				break;
			case 'checkdate':
				$err_msg = "not a valid date";
				break;
			case 'code':
				$err_msg = "only alpha numeric charactors";
				break;
			case 'number':
				switch( $option ) {
					case 'int':
						$err_msg = 'please enter an integer';
						break;
					case 'float':
						$err_msg = 'please enter float value';
						break;
					case 'number':
						$err_msg = 'please enter numbers only';
						break;
					default:
						$err_msg = 'please enter numeric value';
						break;
				}
				break;
			case 'range':
				$err_msg = "invalid range:";
				if( isset( $option['min'] ) ) {
					$err_msg .= " minimum {$option{'min'}}";
				}
				if( isset( $option['max'] ) ) {
					$err_msg .= " miximum {$option{'min'}}";
				}
				if( isset( $option['lt'] ) ) {
					$err_msg .= " less than {$option{'min'}}";
				}
				if( isset( $option['gt'] ) ) {
					$err_msg .= " greater than {$option{'min'}}";
				}
				break;
		}
		return $err_msg;
	}
	// +--------------------------------------------------------------- +
}

class FilterJa
{
	// +--------------------------------------------------------------- +
	function apply() {
		Dio::setFilterClass( __CLASS__ );
		Dio::setFilterMethods( 'mbConvert', TRUE );
		Dio::setFilterMethods( 'hankaku',   FALSE );
		Dio::setFilterMethods( 'hankaku', FALSE );
		Dio::setFilterMethods( 'hankaku', FALSE );
		Dio::setFilterMethods( 'hankaku', FALSE );
	}
	// +--------------------------------------------------------------- +
	function hankaku( $val, $option=array() ) {
		return self::mbConvert( $val, 'hankaku' );
	}
	// +--------------------------------------------------------------- +
	function mbConvert( $val, $option=array() ) {
		if( !$val ) return $val;
		switch( $option ) {
			case 'hankaku':		$str = 'ak';		break;
			case 'han_kata':	$str = 'kh';		break;
			case 'zen_hira':	$str = 'HVc';		break;
			case 'zen_kata':	$str = 'KVC';		break;
			default:			$str = 'ASV';		break;
		}
        $val = mb_convert_kana( $val, $str );
        return $val;
	}
	// +--------------------------------------------------------------- +
}


?>