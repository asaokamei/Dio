<?php
namespace CenaDta\Dio;
use CenaDta\Dio\Filter as Filter;
use CenaDta\Dio\Verify as Verify;

// sample code....

Dio::get( $_POST, 'mail', 'email', 
	array(
		'default'  => 'text@example.com',
		'required' => TRUE,
	), $error );

Dio::verify( $email, 'email', 
	array(
		'default'  => 'text@example.com',
		'required' => TRUE,
	), $error );

Dio::validate( $email, 
	array(
		'secure'   => array( 'charset' => 'UTF-8' ),
		'tolower'  => array(), 
		'sanitize' => array( 'sanitize' => FILTER_SANITIZE_EMAIL ),
		'required' => array( 'required' => TRUE ),
		'default'  => array( 'default' => 'test@example.com', 'loop' => 'break' ),
		'email'    => array(),
	), array(), $error );

/****
****/
class Dio
{
	static $default_charset = 'UTF-8';
	static $default_filters = 
		array(
			  'noNull'      => TRUE,
			  'encoding'    => 'UTF-8',
			  'sanitize'    => FALSE,
			  'tolower'     => FALSE,
			  'letterCase'  => FALSE,
			  );
	static $default_verifies = 
		array(
			  'default'  => FALSE, // default is filter but put it at the beginning of verifies. 
			  'required' => FALSE,
			  'code'     => FALSE,
			  'length' => FALSE,
			  'regexp' => FALSE,
			  );
	
	static $filter_classes = array();
	
	static $filters = array(
		// example of filter setting.
		'some type name' => 
			array(
				  'filter1 name' => array( 'option1' => 'value1', 'option2' => 'value2' ),
				  'filter2 name' => TRUE,   // use filter3, no option.
				  'filter3 name' => FALSE,  // do not use filter3
				  'filter4 name' => 'trim', // use function trim as filter4
				  'filter5 name' => function( $val ){}, // use function. 
				  ),
		// filters for email type.
		'email' => 
			array(
				  'sanitize' => FILTER_SANITIZE_EMAIL,
				  'tolower'  => TRUE,
				  'required' => FALSE,
				  'default'  => FALSE,
				  ),
		);
	// +--------------------------------------------------------------- +
	/** validate a value in $data array. 
	 */
	function get( $data, $name, $type='text', $options=array(), &$error=NULL ) {
		if( !isset( $data[ $name ] ) ) {
			return FALSE;
		}
		$value = $data[ $name ];
		if( !self::verify( $value, $type, $options, $error ) ) {
			$value = FALSE;
		}
		return $value;
	}
	// +--------------------------------------------------------------- +
	/** validate a value based on type. 
	 *  filter-verify-value
	 */
	function verify( &$value, $type='text', $options=array(), &$error=NULL ) {
		$filters = self::$filters[ $type ];
		$filters = array_merge( $filters, $options );
		return self::validate( $value, $filters, $options, $error );
	}
	// +--------------------------------------------------------------- +
	/** filter-verify-value
	 */
	function validate( &$value, $filters=array(), $options=array(), &$error ) 
	{
		$success = TRUE;
		// build filter list. 
		$filters = array_merge( static::$default_filters, static::$default_verifies, $options );
		// filter/verify $value.
		if( !empty( $filters )
		foreach( $filters as $f_name -> $option ) {
			if( $option === FALSE ) continue;
			$success &= self::filter( $value, $f_name, $option, $error, $loop );
			if( $loop == 'break' ) break;
		}
		return $success;
	}
	// +--------------------------------------------------------------- +
	/** main verify method for verifying value using this Verify class. 
	 */ 
	function filter( $value, $f_name, $option, &$error=NULL, &$loop=NULL ) 
	{
		$success = TRUE;
		// -----------------------------------
		// recursively filter on array $value. 
		if( is_array( $value ) ) {
		   // make sure $error is also an array. 
			if( !is_array( $error ) ) { 
				$error = array(); 
			}
			foreach( $value as $key => $val ) {
				$success &= self::filter( $val, $error[$key], $f_name, $option );
			}
			return $success;
		}
		// -----------------------------------
		// filter/verify value. 
		if( is_callable( $option ) ) {
			$success = call_user_func_array( $option, $value );
		}
		else
		if( method_exists( 'Dio', $f_name ) ) {
			$success = Dio::$f_name( $value, $option, $loop );
		}
		else {
			foreach( self::$filter_classes as $class ) {
				if( method_exists( $class, $f_name ) ) {
					$success = $class::$f_name( $value, $option );
				}
			}
		}
		if( !$success ) { // it's an error. set an error message in $error.
			if( isset( $options[ 'err_msg' ] ) ) {
				$error = $options[ 'err_msg' ];
			}
			else {
				$error = "error@{$f_name}";
			}
			if( WORDY ) echo "<font color=red>verify failed( $value, $error, $f_name, $options );</font><br/>\n";
		}
		return $success;
	}
	// +--------------------------------------------------------------- +
	//  preset validator and filter's.
	// +--------------------------------------------------------------- +
	/**
	 */
	function setFilterClass( $class ) {
		static::$filter_classes[] = $class;
	}
	// +--------------------------------------------------------------- +
	/**
	 */
	function setFilterMethods( $method ) {
		static::$default_filters[] = $method;
	}
	// +--------------------------------------------------------------- +
	/**
	 */
	function setVerifyMethods( $method ) {
		static::$default_verifies[] = $method;
	}
	// +--------------------------------------------------------------- +	
	/** verifies if required value has a value. 
	 */
	function required( $value, $option, &$loop=NULL ) 
	{
		if( have_value( $value ) ) { // have value. default is no use...
			return TRUE;
		}
		$required = FALSE;
		if( !is_array( $option ) && $option ) { // required value not there.
			$required = TRUE;
		}
		else
		if( isset( $option[ 'required' ] ) && $option[ 'required' ] ) {
			$required = TRUE;
		}
		if( $required ) {
			if( isset( $option[ 'loop' ] ) && $option[ 'loop' ] == 'break' ) {
				$loop = 'break';
			}
			return FALSE;
		}
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	/** if value is empty, set to default value. 
	 */
	function default( &$value, $option, &$loop=NULL ) {
		if( have_value( $value ) ) { // have value. default is no use...
			return TRUE;
		}
		if( !is_array( $option ) ) { // default value specified. just use it. 
			$value = $option;
			return TRUE;
		}
		if( $option[ 'default' ] ) { // more complex option here. 
			$value = $option[ 'default' ];
		}
		if( $option[ 'break' ]) {
			$loop = 'break';
		}
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	/** minimum security filter. 
	 */
	function secure( &$value, $option=array(), &$loop=NULL ) {
		$val = self::encoding( $value, $option );
		$val = self::noNull( $value,   $option );
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	/** completely filters out if bad encoding. 
	 */
	function encoding( &$value, $option=array(), &$loop=NULL ) {
		if( isset( $option[ 'charset' ] ) ) {
			$charset = $option[ 'charset' ];
		}
		else
		if( mb_internal_encoding() ) {
			$charset = mb_internal_encoding();
		}
		else {
			$charset = self::$default_charset;
		}
		if( !mb_check_encoding( $value, $charset ) ) {
			$value = '';
		}
		return TRUE;
	}
	// +--------------------------------------------------------------- +
	/** filters out Null charactor. 
	 */
	function noNull( &$value, $option=array() ) {
		$value = str_replace( "\0", '', $value );
		return TRUE;
	}
	// +--------------------------------------------------------------- +
}



?>