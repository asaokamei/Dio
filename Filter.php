<?php
namespace CenaDta\Dio;

class Filter
{
	// +--------------------------------------------------------------- +
	// FILTER METHODS!!!
	// +--------------------------------------------------------------- +
	function trim( $value, $option=array() ) {
		return trim( $value );
	}
	// +--------------------------------------------------------------- +
	function toLower( $val, $option=array() ) {
		return strtolower( $val );
	}
	// +--------------------------------------------------------------- +
	function toUpper( $val, $option=array() ) {
		return strtoupper( $val );
	}
	// +--------------------------------------------------------------- +
	function sanitizeEmail( $val, $option=array() ) {
		return self::sanitize( $val, FILTER_SANITIZE_EMAIL, $option )
	}
	// +--------------------------------------------------------------- +
	function sanitize( $val, $filter, $option=array() ) {
		if( !filter_var( $val, $filter, $option  ) ) {
			$val = '';
		}
		return $val;
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