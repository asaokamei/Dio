<?php
namespace CenaDTA\Util;

class WebIO
{
	const ENCODE_NONE         = 'none'; 
	const ENCODE_BASE64       = 'base64'; 
	const ENCODE_CRYPT        = 'crypt';
    public static $save_id    = 'Dio_saveID';  // 
	public static $encode_id  = 'Dio_Encode_Type_'; 
	public static $encode_std = 'base64';
    public static $crypt_pswd = "web_secret_pswd";   // 
	// +--------------------------------------------------------------- +
    static function Web( $save_id=FALSE, $passwd=FALSE ) {
		self::saveId( $save_id );
		self::passWord( $save_id );
    }
	// +--------------------------------------------------------------- +
    static function saveId( $save_id=FALSE ) {
		if( $save_id !== FALSE ) {
			self::$save_id = $save_id; 
		}
		return self::$save_id;
	}
	// +--------------------------------------------------------------- +
    static function passWord( $passwd=FALSE ) {
		if( $passwd !== FALSE ) {
			self::$crypt_pswd = $passwd; 
		}
		return self::$crypt_pswd;
	}
    // +--------------------------------------------------------------- +
    static function savePost( $data, $save_id=NULL, $encode=NULL )
    {
        if( WORDY > 4 ) echo "<i>Web::savePost( $data, $save_id, $encode )</i>...<br>\n";
        if( !Util::isValue( $save_id ) ) { 
            $save_id = self::$save_id; 
        }
		if( !Util::isValue( $encode  ) ) { $encode  = self::ENCODE_CRYPT; }
        
        $val   = self::encodeData( $data, $encode );
        $enc_id= self::$encode_id;
        $htag  = "<input type='hidden' name='{$save_id}' value='{$val}'>";
        $htag .= "<input type='hidden' name='{$enc_id}{$save_id}' value='{$encode}'>\n";
        
        return $htag;
    }
    // +--------------------------------------------------------------- +
    static function loadPost( $save_id='', $encode=NULL )
    {
        if( WORDY > 4 ) echo "<i>Web_IO::loadPost( $save_id, $encode )</i>...<br>\n";
        if( !Util::isValue( $save_id ) ) { 
			$save_id = self::$save_id; 
		}
        if( !$encode && Util::isValue( $_POST[ self::$encode_id . $save_id ] ) ) {
            $encode = $_POST[ self::$encode_id . $save_id ];
        }
        
        $data = array();
        if( Util::isValue( $_POST[ $save_id ] ) ) {
            $data = self::decodeData( $_POST[ $save_id ], $encode );
        }
        return $data;
    }
    // +--------------------------------------------------------------- +
    static function saveSession( $data, $save_id='',$encode=NULL )
    {
        if( WORDY > 4 ) echo "<i>Web_IO::saveSession( $data, $save_id, $encode )</i>...<br>\n";
        if( !Util::isValue( $save_id ) ) { $save_id = self::$save_id; }
		if( !Util::isValue( $encode  ) ) { $encode  = self::$encode_std; }
        
        if( empty( $_SESSION ) ) {
            session_start();
        }
        $_SESSION[ $save_id ] = self::encodeData( $data, $encode );
        
        return TRUE;
    }
    // +--------------------------------------------------------------- +
    static function loadSession( $save_id='',$encode=NULL )
    {
        if( WORDY > 4 ) echo "<i>Web_IO::loadSession( $save_id, $encode )</i>...<br>\n";
        if( !Util::isValue( $save_id ) ) { $save_id = self::$save_id; }
		if( !Util::isValue( $encode  ) ) { $encode  = self::$encode_std; }
        
        $data = NULL;
        if( empty( $_SESSION ) ) {
            session_start();
        }
        if( !empty( $_SESSION[ $save_id ] ) ) {
            $data = self::decodeData( $_SESSION[ $save_id ], $encode );
        }
        return $data;
    }
    // +--------------------------------------------------------------- +
    static function clearSession( $save_id='' )
    {
        if( isset( $_SESSION[ $save_id ] ) ) {
            unset( $_SESSION[ $save_id ]  );
        }
    }
    // +--------------------------------------------------------------- +
    static function saveCookie( $data, $save_id='', $encode=NULL, $save_time='' )
    {
        if( WORDY > 4 ) echo "<i>Web_IO::saveCookie( $data, $save_id, $encode, $save_time )</i>...<br>\n";
        if( !Util::isValue( $save_id ) ) { $save_id = self::$save_id; }
		if( !Util::isValue( $encode  ) ) { $encode  = self::ENCODE_CRYPT; }
        
        $cook_value = self::encodeData( $data, $encode );
        if( !$save_time ) {
            $success = setcookie( $save_id, $cook_value );
        }
        elseif( !is_numeric( $save_time ) ) {
            // $save_time = 60 * 60 * 24 * 365; // save for a year
            // $save_time = 60 * 60 * 24 * 30; // save for 30 days
            // $save_time = 60 * 60 * 24 * 1; // save for 1 days
               $save_time = 60 * 60 * 24 * 1; // save for 1 days
            $success = setcookie( $save_id, $cook_value, time()+$save_time );
        }
        else {
            $success = setcookie( $save_id, $cook_value, time()+$save_time );
        }
        setcookie( self::$encode_id . $save_id, $encode );
        
        if( WORDY > 4 ) echo "setcookie( $save_id, $cook_value );<br>\n";
        if( $success ) {
            if( WORDY > 4 ) echo " -> saved data to COOKIE[{$save_id}]...<br>\n";
        } else {
            if( WORDY ) echo "<font color=red> -> save to cookie failed!</font><br>\n";
        }
        return $success;
    }
    // +--------------------------------------------------------------- +
    static function loadCookie( $save_id='',$encode=NULL )
    {
        if( WORDY > 4 ) echo "<i>Web_IO::loadCookie( $save_id, $encode )</i>...<br>\n";
        if( !Util::isValue( $save_id ) ) { $save_id = self::$save_id; }
        if( !$encode && Util::isValue( $_COOKIE[ self::$encode_id . $save_id ] ) ) {
            $encode = $_COOKIE[ self::$encode_id . $save_id ];
        }
        
        $data = array();
        if( @Util::isValue( $_COOKIE[ $save_id ] ) ) {
            $data = self::decodeData( $_COOKIE[ $save_id ], $encode );
        }
        return $data;
    }
    // +--------------------------------------------------------------- +
    static function encodeData( $data, $encode=NULL )
    {
        if( WORDY > 4 ) echo "<i>Web_IO::encodeData( $data, $encode )</i>...<br>\n";
        // encoding $data; $data can be an array
        // returns a seriarized string data.
		if( !Util::isValue( $encode  ) ) { $encode  = self::$encode_std; }
        $se_data = serialize( $data );
        
        switch( $encode )
        {
            case self::ENCODE_BASE64:
                $en_data = base64_encode( $se_data );
                break;
            case self::ENCODE_CRYPT:
				if( !function_exists( 'mcrypt_encrypt' ) ) {
					throw new Exception( 'mcrypt not installed @' . __CLASS__ , 9999 );
				}
				// from: http://jp.php.net/manual/ja/function.mcrypt-encrypt.php
				$en_data = 
					trim( base64_encode( mcrypt_encrypt( 
								MCRYPT_RIJNDAEL_256, 
								self::$crypt_pswd, 
								$se_data, 
								MCRYPT_MODE_ECB, 
								mcrypt_create_iv( 
									mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), 
									MCRYPT_RAND
								)
					) ) ); 
				break;
            case self::ENCODE_NONE:
            default:
                $en_data = $se_data;
                break;
        }
        if( WORDY > 3 ) {
            echo "encoded: "; print_r( $data ); echo "==> {$se_data} ==> {$en_data}<br>\n";
        }
        return $en_data;
    }
    // +--------------------------------------------------------------- +
    static function decodeData( $data, $encode=PGG_ENCODE_TYPE )
    {
        if( WORDY > 3 ) echo "<i>Web_IO::decodeData( $data, $encode )</i>...<br>\n";
        // decoding $data; $data is a seriarized string data of an PHP variable.
        
        switch( $encode )
        {
            case self::ENCODE_BASE64:
                $de_data = base64_decode( $data );
                break;
            case self::ENCODE_CRYPT:
				if( !function_exists( 'mcrypt_decrypt' ) ) {
					throw new Exception( 'mcrypt not installed @' . __CLASS__ , 9999 );
				}
				// from: http://jp.php.net/manual/ja/function.mcrypt-encrypt.php
				$de_data = 
					trim( mcrypt_decrypt(
							MCRYPT_RIJNDAEL_256, 
							self::$crypt_pswd, 
							base64_decode( $data ), 
							MCRYPT_MODE_ECB, 
							mcrypt_create_iv(
								mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), 
								MCRYPT_RAND
							)
					) ); 
				break;
            case self::ENCODE_NONE:
            default:
                $de_data = $data;
                break;
        }
        $un_data = unserialize( $de_data );
        
        return $un_data;
    }
	// +--------------------------------------------------------------- +
}


?>