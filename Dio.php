<?php
namespace CenaDta\Dio;
use CenaDta\Dio\Filter as Filter;
use CenaDta\Dio\Verify as Verify;

// sample code....

Dio::get( $_POST, 'user_mail', 'email', 
	array(
		'default'  => 'text@example.com',
		'required' => TRUE,
		'string'   => 'lower',
	)
);

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
	// -----------------------------------
	static $default_charset = 'UTF-8';
	// -----------------------------------
	/** default_filters and default_verifies lists available filters, 
	 *  their default parameters, and order to apply filters. 
	 *  if filter is not listed in these default, it will be 
	 *  applied at the end of the list. So, make sure your filters 
	 *  are listed in default_filters. 
	 */
	static $default_filters = 
		array(
			  'multiple'    => FALSE,
			  'noNull'      => TRUE,
			  'encoding'    => 'UTF-8',
			  'mbCheckKana' => 'standard', 
			  'sanitize'    => FALSE,
			  'date'        => FALSE,
			  'time'        => FALSE,
			  'string'      => FALSE,
			  );
	// -----------------------------------
	/** same as default_filters but it lists verifiers. 
	 */
	static $default_verifies = 
		array(
			  'default'    => FALSE, // default is filter but put it at the beginning of verifies. 
			  'required'   => FALSE,
			  'code'       => FALSE,
			  'length'     => FALSE,
			  'regexp'     => FALSE,
			  'number'     => FALSE,
			  'range'      => FALSE,
			  'checkdate'  => FALSE,
			  );
	// -----------------------------------
	static $filter_options = array(
		'lower'       => array( 'string',   'tolower' ),
		'upper'       => array( 'string',   'toupper' ),
		'capital'     => array( 'string',   'tocapital' ),
		'code'        => array( 'regexp',   '[-_0-9a-zA-Z]*' ),
		'datetype'    => array( 'regexp', 
		                                    'ymd'  => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
		                                    'ym'   => '[0-9]{4}-[0-9]{2}',
		                                    'His'  => '[0-9]{2}:[0-9]{2}:[0-9]{2}',
		                                    'Hi'   => '[0-9]{2}:[0-9]{2}',
		                                    'dt'   => '[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}',
											'code' => '[-_0-9a-zA-Z]*',
							  ),
		'number'      => array( 'regexp',   '[0-9]*', 
							                'int'    => '[-]{0,1}[0-9]*',
								            'float'  => '[-]{0,1}[.0-9]*', 
							  ),
		'jaKatakana'  => array( 'mbJaKana', 'standard' ),
		'hankaku'     => array( 'mbJaKana', 'hankaku' ),
		'hankana'     => array( 'mbJaKana', 'hankana' ),
	);
	
	// -----------------------------------
	static $filter_classes = array();
	
	// -----------------------------------
	static $filters = array(
		// example of filter setting.
		'some type name' => 
			array(
				'filter1 name' => array( 'option1' => 'value1', 'option2' => 'value2' ),
				'filter2 name' => TRUE,   // use filter2, no option.
				'filter3 name' => FALSE,  // do not use filter3
				'filter4 name' => 'trim', // use function trim as filter4
				'filter5 name' => function( $val ){}, // use function. 
				'mbJaKana'     => TRUE, 
				),
		// filters for email type.
		'email' => 
			array(
				'mbConvert'  => 'hankaku',
				'sanitize'   => FILTER_SANITIZE_EMAIL,
				'string'     => 'tolower',
				'required'   => FALSE,
				'default'    => FALSE,
				),
		'number'  =>
			array(
				'mbConvert'   => 'hankaku',
				'mbCheckKana' => 'hankaku_only',
				'number'      => TRUE,
			),
		'int'  =>
			array(
				'number'    => 'int',
			),
		'float'  =>
			array(
				'number'    => 'float',
			),
		'date' =>
			array(
				'multiple'  => array( 'suffix' => array( 'y', 'm', 'd' ), 
				                      'connector' => '-' 
				                    ),
				'datetype'  => 'ymd',
				'checkdate' => TRUE,
			),
		'ym' =>
			array(
				'multiple'  => array( 'suffix' => array( 'y', 'm' ),
				                      'connector' => '-' 
				                    ),
				'datetype'  => 'ym',
			),
		'time' =>
			array(
				'multiple'  => array( 'suffix' => array( 'h', 'i', 's' ), 
				                      'connector' => ':' 
				                    ),
				'datetype'  => 'His',
			),
		'datetime' =>
			array(
				'multiple'  => array( 'suffix'  => array( 'y', 'm', 'd', 'h', 'i', 's' ), 
				                      'sformat' => '%04d-%02d-%02d %02d:%02d:%02d' 
				                    ),
				'datetype'  => 'dt',
			),
		);
	// +--------------------------------------------------------------- +
	/** validate a value based on type. 
	 *  filter-verify-value
	 */
	function verify( &$value, $type='text', $options=array(), &$error=NULL ) {
		$filters = self::$filters[ $type ];
		$filters = array_merge( $filters, $options );
		return self::validate( $value, $filters, $error );
	}
	// +--------------------------------------------------------------- +
	/** get a validated value in $data array. 
	 */
	function get( $data, $name, $type='text', $options=array(), &$error=NULL ) {
		$filters = self::$filters[ $type ];
		$filters = array_merge( $filters, $options );
		$value = self::find( $data, $name, $filters );
		if( !self::validate( $value, $options, $error ) ) {
			$value = FALSE;
		}
		return $value;
	}
	// +--------------------------------------------------------------- +
	/** get a validated value in $_REQUEST array. 
	 */
	function request( $name, $type='text', $options=array(), &$error=NULL, $data=NULL ) {
		if( $data === NULL ) $data = $_REQUEST;
		return Dio::get( $data, $name, $type, $options, $error );
	}
	// +--------------------------------------------------------------- +
	/** find a value $name in an array, $data. 
	 *  if multiple option is set, get as multiple value. 
	 */
	function find( $data, $name, $filters=FALSE ) {
		if( isset( $data[ $name ] ) ) {
			$value = $data[ $name ];
		}
		else
		if( isset( $filters[ 'multiple' ] ) && $filters[ 'multiple' ] !== FALSE ) {
			$value = self::multiple( $data, $name, $filters[ 'multiple' ] )
		}
		else {
			$value = FALSE;
		}
		return $valeu;
	}
	// +--------------------------------------------------------------- +
	/** a special method to obtain multiple value from a data. 
	 *  not to be used like other filters. 
	 */
	function multiple( $data, $name, $option ) 
	{
		// get multiple value as specified by $option. 
		// $option['suffix']={ $sfx1, $sfx2 }: list of suffix
		// $option['connecter']='string': 
		// $option['separator']='string': 
		// $option['sformat']  = sprintf's format. overwrites connector.
		$sep  = '_';
		$con  = '-';
		if( isset( $option['separator'] ) ) {
			$sep = $option['separator'];
		}
		if( isset( $option['connecter'] ) ) {
			$con = $option['connecter'];
		}
		$found = array();
		foreach( $option[ 'suffix' ] as $sfx ) {
			$name_sfx = $name . $sep . $sfx;
			if( !isset( $data[ $name_sfx ] ) ) {
				$found[] = $data[ $name_sfx ];
			}
		}
		if( empty( $found ) ) {
			$found = FALSE;
		}
		else
		if( isset( $option[ 'sformat' ] ) ) {
			$option = array_merge( array( $option[ 'sformat' ], $found );
			$found = call_user_func_array( 'sprintf', $option );
		}
		else {
			$found = implode( $con, $found );
		}
		return $found;
	}
	// +--------------------------------------------------------------- +
	/** filter-verify-value
	 */
	function validate( &$value, $filters=array(), &$error ) 
	{
		$success = TRUE;
		// build filter list. 
		$filters = array_merge( static::$default_filters, static::$default_verifies, $options );
		// filter/verify $value.
		if( !empty( $filters )
		foreach( $filters as $f_name -> $option ) 
		{
			if( $option === FALSE ) continue;
			if( $f_name == 'multiple' ) continue;
			$success = self::filter( $value, $f_name, $option, $error, $loop );
			if( $success === FALSE ) break;
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
				$success &= self::filter( $value[$key], $f_name, $option, $error[$key], $loop );
			}
			return $success;
		}
		// -----------------------------------
		// preprocess $option and $err_msg.
		if( is_array( $option ) && isset( $option[ 'err_msg' ] ) ) {
			$err_msg = $option[ 'err_msg' ];
			unset( $option[ 'err_msg' ] );
			if( count( $option ) == 1 ) { // reduce array to string.
				foreach( $option as $opt ) {}
				$option = $opt;
			}
		}
		else {
			$err_msg = "error@{$f_name}";
		}
		// -----------------------------------
		// determine real $filter from $f_name and $option. 
		if( !isset( self::$filter_options[ $f_name ] ) ) {
			// simple case. $f_name is not listed in filter option. 
			$filter = $f_name;
		}
		else 
		if( !is_array( self::$filter_options[ $f_name ] ) ) {
			// use different name in the filter list. 
			$filter = self::$filter_options[ $f_name ];
		}
		else {
			// or more complicated case if is an array...
			// the first item is always the filter name. 
			$filter = self::$filter_options[ $f_name ][0];
			// now, get the option. 
			if( !is_array( $option ) && 
				isset( self::$filter_options[ $f_name ][ $option ] ) ) {
				// use predefined option. 
				$option = self::$filter_options[ $f_name ][ $option ];
			}
			else 
			if( isset( self::$filter_options[ $f_name ][1] ) ) {
				// use option in filter_potions...
				$option = self::$filter_options[ $f_name ][1]
			}
			// use option as is. 
		}
		// -----------------------------------
		// filter/verify value. 
		if( is_callable( $option ) ) {
			$success = call_user_func_array( $option, $value );
		}
		else
		if( method_exists( 'Dio', $filter ) ) {
			$success = Dio::$filter( $value, $option, $loop );
		}
		else {
			foreach( self::$filter_classes as $class ) {
				if( method_exists( $class, $filter ) ) {
					$success = $class::$filter( $value, $option );
				}
			}
		}
		if( !$success ) { // it's an error. set an error message in $error.
			if( $err_msg ) { // use err_msg in option. 
				$error = $err_msg;
			}
			else
			if( $err_msg =  ) {
			}
			else { // use generic error message. 
				$err_msg = "error@{$f_name}";
			}
			if( WORDY ) echo "<font color=red>verify failed( $value, $error, $f_name ), err_msg={$err_msg}</font><br/>\n";
		}
		return $success;
	}
	// +--------------------------------------------------------------- +
	//  modify filter settings.
	// +--------------------------------------------------------------- +
	/**
	 */
	function setFilterClass( $class ) {
		static::$filter_classes[] = array_merge( array( $class ), static::$filter_classes );
	}
	// +--------------------------------------------------------------- +
	/**
	 */
	function addFilterMethods( $name, $method ) {
		static::$default_filters[ $name ] = $method;
	}
	// +--------------------------------------------------------------- +
	/**
	 */
	function addVerifyMethods( $name, $method ) {
		static::$default_verifies[ $name ] = $method;
	}
	// +--------------------------------------------------------------- +
	/**
	 */
	function addFilterOptions( $name, $option ) {
		static::$filter_options[ $name ] = $option;
	}
	// +--------------------------------------------------------------- +
	//  preset validator and filter's.
	// +--------------------------------------------------------------- +
	/** verifies if required value has a value. 
	 */
	function required( $value, $option, &$loop=NULL ) 
	{
		if( have_value( $value ) ) { // have value. must be OK...
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
		if( is_array( $option ) && isset( $option[ 'charset' ] ) ) {
			$charset = $option[ 'charset' ];
		}
		else
		if( have_value( $option ) ) {
			$charset = $option;
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