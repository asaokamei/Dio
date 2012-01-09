<?php
/**
 * view object for generating html page.
 * data-transfer-object.
 */

class DataObj
{
    // ---------------------------------
    /**
     * stores data used in/out of Page instance.
     */
    var $data = array();
    // +-------------------------------------------------------------+
    //  set/get methods for storing data into this instance.
    // +-------------------------------------------------------------+
    /**
     * sets data to Control instace.
     *
     * @param string/array $name
     *        name of variable (string)
     *        array of data: name=>val
     * @param null $val
     *        value for name
     * @return object
     *        returns $this.
     */
    function set( $name, $val=NULL ) {
        if( $val === NULL ) {
            if( is_array( $name ) ) {
                $this->data = array_merge( $this->data, $name );
            }
        }
        else
            $this->data[ $name ] = $val;
        if( WORDY > 1 ) @wordy_table( $this->data[ $name ], "addData: ".$val );
        return $this;
    }
    // +-------------------------------------------------------------+
    /**
     *    get data from Control instance.
     *
     * @param null $name
     *        $name specify name of value to get.
     *        if omitted, returns all of data.
     * @return mix
     *        returns data.
     */
    function get( $name=NULL ) {
        if( $name !== NULL ) {
            return $this->data[ $name ];
        }
        else {
            // return all data.
            return $this->data;
        }
    }
    // +-------------------------------------------------------------+
    /**
     * setter/getter combined method
     * @param $name        name of data.
     * @param null $val    sets value of the data if given.
     * @return mix|object  returns value of data.
     */
    function data( $name, $val=NULL ) {
        if( $val !== NULL ) {
            $this->set( $name, $val );
        }
        return $this->get( $name );
    }
    // +-------------------------------------------------------------+
}


class View
{
    var $currAct      = FALSE;      // current action
    var $nextAct      = FALSE;      // what's next?
    // ---------------------------------
    /**
     * stores html page info
     */
    var $html = FALSE;
    // ---------------------------------
    /**
     * stores data used in/out of Page instance.
     */
    var $data = FALSE;
    // ---------------------------------
    /**
     *	for messages and error controls
     */
    var $message = FALSE;
    // +-------------------------------------------------------------+
    /**
     *	constructor. initializes pgg.
     */
    function __construct( $option=array() ) {
        $this->_init( $option );
    }
    // +-------------------------------------------------------------+
    function _init( $option=array() ) {
        if( isset( $option[ 'pgg' ] ) ) {
            $this->pgg = $option[ 'pgg' ];
        }
        else {
            $this->pgg = new \pgg_check( self::PGG_ID );
            $this->pgg->restorePost();
        }
        if( isset( $option[ 'data' ] ) ) {
            $this->data = $option[ 'data' ];
        }
        else {
            $this->data = new DataObj();
        }
        if( isset( $option[ 'html' ] ) ) {
            $this->html = $option[ 'html' ];
        }
        else {
            $this->html = new DataObj();
        }
        if( isset( $option[ 'message' ] ) ) {
            $this->message = $option[ 'message' ];
        }
        else {
            $this->message = new Message();
        }
    }
    // +-------------------------------------------------------------+
    function set( $name, $val=NULL ) {
        return $this->data->set( $name, $val );
    }
    // +-------------------------------------------------------------+
    function get( $name=NULL ) {
        return $this->data->get( $name );
    }
    // +-------------------------------------------------------------+
    //  set/get methods for html page info.
    // +-------------------------------------------------------------+
    function html( $name, $val=NULL ) {
        return $this->html->data( $name, $val );
    }
    // +-------------------------------------------------------------+
    function currTitle( $title ) {
        return $this->html( 'currTitle', $title );
    }
    // +-------------------------------------------------------------+
    function nextTitle( $title ) {
        return $this->html( 'nextTitle', $title );
    }
	// +-------------------------------------------------------------+
}

