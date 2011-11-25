<?php
namespace CenaDta\Util;

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
    /**
     * constructor
     * @param string $save_id    used as save_id name. 
     */
    function __construct( $save_id=NULL ) {
        $this->setSaveId( $save_id );
        $this->data_source = array(
            'Data'   => & $this->src_data,
            'Post'   => & $_POST,
            'Get'    => & $_GET,
            'Cookie' => & $_COOKIE,
        );
    }
    // +--------------------------------------------------------------- +
    /**
     * set save_id.
     * @param string $save_id    sets save_id.
     * @return $this
     */
    function setSaveId( $save_id ) {
        if( Util::isValue( $save_id ) ) $this->save_id = $save_id;
        return $this;
    }
    // +--------------------------------------------------------------- +
    /**
     * find variable from $this->data_source. 
     * 
     * @param mix $value    returns found value. 
     * @param string $name  name of value to find.
     * @param string $type  type of value to find.
     * @param array $filter extra filter.
     * @return string       returns found source, or FALSE if not found.
     */
    function find( &$value, $name, $type='asis', $filter=array() ) {
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
    /**
     * find variable and set to $this->data[$name].
     * if error, set to $this->err_msg[$name].
     * 
     * @param type $name    name of value to find.
     * @param type $type    type of value to find.
     * @param type $filter  extra filter.
     * @return $this
     */
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
    /**
     * pop $name value. same as get. 
     * @param type $name  name of value. 
     * @return mix        the value.
     */
    function pop( $name=FALSE ) {
        return $this->get( $name );
    }
    // +--------------------------------------------------------------- +
    /**
     * pop $name for HTML display (htmlspecialchars'ed). 
     * @param type $name  name of value.
     * @return type       the html-safe value. 
     */
    function popHtml( $name=FALSE ) {
        $return = $this->pop( $name );
        array_walk_recursive( $return, 
            function( &$v, $k ) { $v = htmlspecialchars( $v, ENT_QUOTES ); } 
        );
        return $return;
    }
    // +--------------------------------------------------------------- +
    /**
     * checks if error for given $name. 
     * @param type $name      name of value to check for error.
     * @param type $err_msg   error message if error. 
     * @return boolean        TRUE if error, FALSE if not an error. 
     */
    function isError( $name, &$err_msg=NULL ) {
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
    /**
     * setter for $name. stores at $this->data[$name]. 
     * @param type $name   name of value to set.
     * @param type $value  the value. 
     * @return $this.
     */
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
    /**
     * getter for $name. if $name is not given, return all the data.
     * @param type $name  name of value to get. 
     * @return mix        returns the named value, 
     *                     or all data if name is not given. 
     */
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
    /**
     * sets data source. 
     * @param array $source  data source to use
     * @param boolena $only  set to TRUE to use this source only. 
     * @param string $title  title of data source (default: 'myData').
     * @return $this
     */
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
    /**
     * sets errors. 
     * @param type $name     name of value where error occured.
     * @param type $err_msg  error message for the value. 
     * @param type $value    errored value. 
     * @return $this
     */
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
    function saveSession() {
        return  $this->saveMethod( "saveSession" );
    }
    // +--------------------------------------------------------------- +
    function & loadSession( $restore=FALSE ) {
        return $this->loadMethod( "loadSession", $restore );
    }
    // +--------------------------------------------------------------- +
    function clearSession() {
        if( isset( $_SESSION[ $this->save_id ] ) ) {
            unset( $_SESSION[ $this->save_id ] );
        }
        return $this;
    }
    // +--------------------------------------------------------------- +
    function savePost() {
        return  $this->saveMethod( "savePost" );
    }
    // +--------------------------------------------------------------- +
    function loadPost( $restore=FALSE ) {
        return $this->loadMethod( "loadPost", $restore );
    }
    // +--------------------------------------------------------------- +
    function saveCookie() {
        return  $this->saveMethod( "saveCookie" );
    }
    // +--------------------------------------------------------------- +
    function loadCookie( $restore=FALSE ) {
        return $this->loadMethod( "loadCookie", $restore );
    }
    // +--------------------------------------------------------------- +
    function saveMethod( $method ) {
        $success  = Web_IO::$method( $this->data, $this->save_id );
        if( WORDY > 1 ) wordy_table( $save_var, "saved data by $method ( $save_id ) into $var_name" );
        return $this;
    }
    // +--------------------------------------------------------------- +
    function loadMethod( $method, $restore=FALSE ) {
        $data = Web_IO::$method( $this->save_id );
        if( !empty( $data ) ) {
            $this->src_data = array_merge( $this->src_data, $data );
            if( $restore ) {
                $this->data = array_merge( $this->data, $data );
            }
        }
        if( WORDY > 1 ) wordy_table( $this->src_data, "loading data by $method( $this->save_id )" );
        return $this;
    }
    // +--------------------------------------------------------------- +
}





?>