<?php
namespace CenaDta\Dio;

class Data
{
    /** save name for post/session/etc.  */
	var $save_id = 'DioData.save.ID';
    
    /** main repository of data */
	var $data     = array();
    var $src_data = array();
	var $err_msg  = array();
	var $err_num  = 0;
	// +--------------------------------------------------------------- +
	function __construct( $save_id=NULL ) {
		if( Util::isValue( $save_id ) ) $this->save_id = $save_id;
        $this->data_source = array(
            'Data'   => & $this->src_data,
            'Post'   => & $_POST,
            'Get'    => & $_GET,
            'Cookie' => & $_COOKIE,
        );
	}
	// +--------------------------------------------------------------- +
    /**
     * 
     */
    function find( &$value, $name, $type, $filter=array() ) {
        $found = FALSE;// not found
        $value = NULL; // not found
        foreach( $this->data_source as $name => $source ) {
            $value = Validator::_find( $source, $name, $filter, $type );
            if( Util::isValue( $value ) ) {
                $found = $name;
                break;
            }
        }
        return $found;
    }
	// +--------------------------------------------------------------- +
	//  push and pop
	// +--------------------------------------------------------------- +
	function push( $name, $type='asis', $filter=array() ) {
        $found = $this->find( $value, $name, $type, $filter );
        if( Validator::validate( $value, $type, $filter, $error ) ) {
            $this->set( $name, $value );
        }
        else { // fails
            $this->setError( $name, $error, $value );
        }
		return $this;
	}
	// +--------------------------------------------------------------- +
	function pop( $name=FALSE ) {
		return $this->get( $name );
	}
	// +--------------------------------------------------------------- +
	function popHtml( $name=FALSE ) {
		$return = $this->data;
		array_walk_recursive( $return, 
			function( &$v, $k ) { $v = htmlspecialchars( $v, ENT_QUOTES ); } 
		);
		return $return;
	}
	// +--------------------------------------------------------------- +
	function isError( $name, &$err_msg ) {
		if( $err_msg = $this->getError( $name ) ) {
			return TRUE;
		}
		$err_msg = '';
		return FALSE;
	}
	// +--------------------------------------------------------------- +
	function popError( $name=FALSE ) {
		return $this->getError( $name );
	}
	// +--------------------------------------------------------------- +
	//  setter and getter
	// +--------------------------------------------------------------- +
	function set( $name, $value ) {
		if( is_array( $name ) ) {
			$this->data = array_merge( $this->data, $name );
		}
		else {
			$this->data[ $name ] = $value;
		}
		return $this;
	}
	// +--------------------------------------------------------------- +
	function get( $name=FALSE ) {
		if( $name === FALSE ) {
			return $this->data;
		}
		if( isset( $this->data[ $name ] ) ) {
			return $this->data[ $name ];
		}
		return FALSE;
	}
	// +--------------------------------------------------------------- +
	function setSource( $source, $only=FALSE, $title='myData' ) {
		if( $only ) {
			$this->data_source = array( $title => $source );
		}
		else {
			$this->data_source = 
				array_merge( array( $title => $source ), $this->data_source );
		}
		return $this;
	}
	// +--------------------------------------------------------------- +
	function setError( $name, $err_msg, $value=FALSE ) {
		$this->err_msg[ $name ] = $err_msg;
		if( $value !== FALSE ) {
			$this->data[ $name ] = $value;
		}
        $this->err_num ++;
		return $this;
	}
	// +--------------------------------------------------------------- +
	function getError( $name=FALSE ) {
		if( $name === FALSE ) {
			return $this->err_msg;
		}
		else
		if( isset( $this->err_msg[ $name ] ) ) {
			return $this->err_msg[ $name ];
		}
		return FALSE;
	}
	// +--------------------------------------------------------------- +
	//  save and load data
	// +--------------------------------------------------------------- +
    function saveSession( $save_id=NULL, $encode=NULL ) {
		return  $this->saveMethod( "saveSession", $save_id, $encode, $vars );
    }
	// +--------------------------------------------------------------- +
    function & loadSession( $save_id=NULL, $encode=NULL ) {
		return $this->loadMethod( "loadSession", $save_id, $encode );
    }
	// +--------------------------------------------------------------- +
    function & restoreSession( $save_id=NULL, $encode=NULL ) {
		return $this->loadMethod( "loadSession", $save_id, $encode, TRUE );
    }
	// +--------------------------------------------------------------- +
    function clearSession( $save_id=NULL ) {
        if( WORDY ) echo "clearSession( $save_id, $encode )";
        if( !have_value( $save_id ) ) $save_id = $this->save_id;
        if( isset( $_SESSION[ $save_id ] ) ) {
            unset( $_SESSION[ $save_id ] );
        }
    }
	// +--------------------------------------------------------------- +
    function savePost( $save_id=NULL, $encode=NULL ) {
		return  $this->saveMethod( "savePost", $save_id, $encode );
    }
	// +--------------------------------------------------------------- +
    function loadPost( $save_id=NULL, $encode=NULL ) {
		return $this->loadMethod( "loadPost", $save_id, $encode );
    }
	// +--------------------------------------------------------------- +
    function restorePost( $save_id=NULL, $encode=NULL ) {
		return $this->loadMethod( "loadPost", $save_id, $encode, TRUE );
    }
	// +--------------------------------------------------------------- +
    function saveCookie( $save_id=NULL, $encode=NULL ) {
		return  $this->saveMethod( "saveCookie", $save_id, $encode );
    }
	// +--------------------------------------------------------------- +
    function loadCookie( $save_id=NULL, $encode=NULL ) {
		return $this->loadMethod( "loadCookie", $save_id, $encode );
    }
	// +--------------------------------------------------------------- +
    function saveMethod( $method, $save_id=NULL, $encode=NULL ) {
        if( !Util::isValue( $save_id ) ) $save_id = $this->save_id;
        $success  = Web_IO::$method( $this->data, $save_id, $encode );
        if( WORDY > 1 ) wordy_table( $save_var, "saved data by $method ( $save_id, $encode ) into $var_name" );
		return $this;
    }
	// +--------------------------------------------------------------- +
    function loadMethod( $method, $save_id='', $encode='', $restore=FALSE ) {
        if( !Util::isValue( $save_id ) ) $save_id = $this->save_id;
		$data = Web_IO::$method( $save_id, $encode );
		if( !empty( $data ) ) {
	        $this->src_data = array_merge( $this->src_data, $data );
			if( $restore ) {
				$this->data = array_merge( $this->data, $data );
			}
        }
        if( WORDY > 1 ) wordy_table( $this->src_data, "loading data by $method( $save_id, $encode )" );
		return $this;
    }
	// +--------------------------------------------------------------- +
}





?>