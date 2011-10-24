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
		if( WORDY > 5 ) echo "string( $value, $option )";
		if( $option == 'lower' ) {
			$value = strtolower( $value );
		}
		else
		if( $option == 'upper' ) {
			$value = strtoupper( $value );
		}
		else
		if( $option == 'capital' ) {
			$value = strcapital( $value );
		}
		if( WORDY > 5 ) echo " --> result=$value <br>";
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	function sanitizeEmail( &$value, $option=array() ) {
		$value = self::sanitize( $value, FILTER_SANITIZE_EMAIL, $option );
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
	/** check if value is a code, number, alpahbets, and '-' & '_'.
	 */
	function code( $val, $option ) {
		return self::regexp( $val, '[-_0-9a-zA-Z]*' );
	}
	// +--------------------------------------------------------------- +
	/** check if date is a valid date with checkdate function. 
	 *  assume date is in 'YYYY-MM-DD' format. 
	 *  specify $option[ dbar ] (i.e. '/'.) if date is 'YYYY/MM/DD'. 
	 */
	function checkDate( $val, $option ) {
		if( !have_value( $val ) ) return FALSE;
		$dbar = '-';
		if( isset( $option[ 'dbar' ] ) ) $dbar = $option[ 'dbar' ];
		list( $year, $month, $day ) = explode( $dbar, $date );
		if( have_value( $year ) && have_value( $month ) && have_value( $day ) ) {
			return @checkdate( $month, $day, $year );
		}
		return FALSE;
	}
	// +--------------------------------------------------------------- +
	/** checks for easy mail format. 
	 */
	function checkMail( $val, $option ) {
		return self::regexp( $val, "[a-zA-Z0-9_.-]+@[a-zA-Z0-9_.-]+\.[a-zA-Z]+" );
	}
	// +--------------------------------------------------------------- +
	/** checks for length of character. 
	 *  $option specifies the exact length of char if in numeric.
	 *  $option[ min ] speficies the minimum length. 
	 *  $option[ max ] speficies the maximum length. 
	 */
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
	function range( $val, $option ) {
		$ok  = TRUE;
		if( !is_numeric( $val ) ) return FALSE;
		if( isset( $option[ 'min' ] ) && $val < $option[ 'min' ] ) {
			$ok = FALSE;
		}
		if( isset( $option[ 'max' ] ) && $val > $option[ 'max' ] ) {
			$ok = FALSE;
		}
		if( isset( $option[ 'lt' ] ) && $val < $option[ 'lt' ] ) {
			$ok = FALSE;
		}
		if( isset( $option[ 'gt' ] ) && $val > $option[ 'gt' ] ) {
			$ok = FALSE;
		}
		if( isset( $option[ 'le' ] ) && $val <= $option[ 'le' ] ) {
			$ok = FALSE;
		}
		if( isset( $option[ 'ge' ] ) && $val >= $option[ 'ge' ] ) {
			$ok = FALSE;
		}
		return $ok;
	}
	// +--------------------------------------------------------------- +
	/**
	 */
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

class FilterJa extends Filter
{
	// +--------------------------------------------------------------- +
	function apply() {
		Dio::setFilterClass( __CLASS__ );
		Dio::setFilterMethods( 'mbConvert', TRUE );
		Dio::setFilterMethods( 'mbCheckKana',   FALSE );
		Dio::setFilterMethods( 'hankaku', FALSE );
		Dio::setFilterMethods( 'hankaku', FALSE );
		Dio::setFilterMethods( 'hankaku', FALSE );
	}
	// +--------------------------------------------------------------- +
	/** only zenkaku-katakana allowed. 
	 */
	function zenKanaOnly( $val, $option=array() ) {
		return self::mbCheckKana( $val, 'zen_kana_only' );
	}
	// +--------------------------------------------------------------- +
	/** only hankaku-katakana allowed. 
	 */
	function hanKanaOnly( $val, $option=array() ) {
		return self::mbCheckKana( $val, 'han_kana_only' );
	}
	// +--------------------------------------------------------------- +
	/** only zenkaku-hiragana allowed. 
	 */
	function zenHiraOnly( $val, $option=array() ) {
		return self::mbCheckKana( $val, 'zen_hira_only' );
	}
	// +--------------------------------------------------------------- +
	/** only hankaku allowed. 
	 */
	function hanKakuOnly( $val, $option=array() ) {
		return self::mbCheckKana( $val, 'hankaku_only' );
	}
	// +--------------------------------------------------------------- +
	/** checks for kana type. 
	 */
	function mbCheckKana( $val, $option=array() ) {
        switch( $convert_str ) 
        {
            case 'zen_kana_only': // only zenkaku-katakana
                $ereg_str = "^[　ー−‐ァ-ヶ]*$";
                break;
            case 'han_kana_only': // only hankaku-katakana
                $ereg_str = "^[ -ヲ-゜]*$";
                break;
            case 'zen_hira_only': // only zenkaku-hiragana
                $ereg_str = "^[　ー−‐ぁ-ん]*$";
                break;
            case 'hankaku_only': // only hankaku
                $ereg_str = "^[ !-~]*$";
                break;
            default:
                $ereg_str = "";
                break;
        }
		if( $ereg_str ) {
			return mb_ereg( "^{$ereg_expr}$", $value );
		}
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	/**
	 */
	function mbConvert( $val, $option=array() ) {
		if( !$val ) return $val;
		switch( $option ) {
			case 'hankaku':		$str = 'ak';		break;
			case 'han_kana':	$str = 'kh';		break;
			case 'zen_hira':	$str = 'HVc';		break;
			case 'zen_kana':	$str = 'KVC';		break;
			default:			$str = 'ASV';		break;
		}
        $val = mb_convert_kana( $val, $str );
        return $val;
	}
	// +--------------------------------------------------------------- +
}


?>