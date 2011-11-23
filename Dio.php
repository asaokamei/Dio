<?php
namespace CenaDta\Dio;
require_once( './Util.php' );
require_once( './Filter.php' );
use CenaDta\Dio\Filter as Filter;
use CenaDta\Dio\Verify as Verify;
Dio::__init( array() );

class Dio
{
    // -----------------------------------
    const  DEFAULT_EMPTY_VALUE = '';
    static $default_charset = 'UTF-8';
    // -----------------------------------
    /** default_filters and default_verifies lists available filters, 
     *  their default parameters, and order to apply filters. 
     *  if filter is not listed in these default, it will be 
     *  applied at the end of the list. So, make sure your filters 
     *  are listed in default_filters. 
     */
    static $default_filters = array(
              'multiple'    => FALSE,
              'noNull'      => TRUE,
              'encoding'    => 'UTF-8',
              'mbConvert'   => 'standard', 
              'trim'        => TRUE,
              'sanitize'    => FALSE,
              'date'        => FALSE,
              'time'        => FALSE,
              'string'      => FALSE,
    );
    /** same as default_filters but it lists verifiers. 
     */
    static $default_verifies = array(
              'default'    => FALSE, // default is filter but put it at the beginning of verifies. 
              'required'   => FALSE,
              'code'       => FALSE,
              'maxlength'  => FALSE,
              'pattern'    => FALSE,
              'number'     => FALSE,
              'min'        => FALSE,
              'max'        => FALSE,
              'range'      => FALSE,
              'checkdate'  => FALSE,
              'mbCheckKana' => FALSE,
              'sameas'     => FALSE,
              'samewith'   => FALSE,
              'sameempty'  => FALSE,
    );
    // -----------------------------------
    /** overwrites options for given filter.
     *  'filter name' => option,
     *   
     *   if option is string -> use this option.
     *   if option is array, real method name, and shorthand option can be set
     *   $option = array(
     *          0       => 'method name',
     *          1       => 'option to use',
     *        'opt1'    => 'real option 1',
     *        'opt...'  => 'real option 2',
     *      )
     *     option[ sub option name ]
     *
     */
    static $filter_options = array(
        'trim'        => 'CenaDTA\Dio\Filter::trim',
        'default'     => 'CenaDTA\Dio\Filter::setDefault',
        'noNull'      => 'CenaDTA\Dio\Filter::noNull',
        'encoding'    => 'CenaDTA\Dio\Filter::encoding',
        'required'    => 'CenaDTA\Dio\Filter::required',
        'pattern'     => 'CenaDTA\Dio\Filter::pattern',
        'lower'       => array( 'CenaDTA\Dio\Filter::string',   'lower' ),
        'upper'       => array( 'CenaDTA\Dio\Filter::string',   'upper' ),
        'capital'     => array( 'CenaDTA\Dio\Filter::string',   'capital' ),
        'code'        => array( 'CenaDTA\Dio\Filter::pattern',  '[-_0-9a-zA-Z]*'
                              ),
        'datetype'    => array( 'CenaDTA\Dio\Filter::pattern', 
                                'ymd'  => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
                                'ym'   => '[0-9]{4}-[0-9]{2}',
                                'His'  => '[0-9]{2}:[0-9]{2}:[0-9]{2}',
                                'Hi'   => '[0-9]{2}:[0-9]{2}',
                                'dt'   => '[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}',
                              ),
        'number'      => array( 'CenaDTA\Dio\Filter::pattern', '[-0-9]*', 
                                'number' => '[0-9]*',
                                'int'    => '[-]{0,1}[0-9]*',
                                'float'  => '[-]{0,1}[.0-9]*', 
                              ),
        'jaKatakana'  => array( 'CenaDTA\Dio\FilterJa::mbJaKana', 'standard' ),
        'hankaku'     => array( 'CenaDTA\Dio\FilterJa::mbJaKana', 'hankaku' ),
        'hankana'     => array( 'CenaDTA\Dio\FilterJa::mbJaKana', 'hankana' ),
        'sameas'      => 'CenaDTA\Dio\Filter::sameas',
        'string'      => 'CenaDTA\Dio\Filter::string',
        'sanitize'    => 'CenaDTA\Dio\Filter::sanitize',
        'checkMail'   => 'CenaDTA\Dio\Filter::checkMail',
        'mbConvert'   => 'CenaDTA\Dio\FilterJa::mbConvert',
        'mbCheckKana' => 'CenaDTA\Dio\FilterJa::mbCheckKana',
    );
    
    // -----------------------------------
    /** error messages for filter. 
     */
    static $default_err_msgs = array(
            'required'    => 'required field',
            'encoding'    => 'invalid characters',
            'sameas'      => 'values are different',
            'sameempty'   => 'enter value to compare',
    );
    
    // -----------------------------------
    static $filters = array(
        /*
        // example of filter setting.
        'some type name' => 
            array(
                'filter1 name' => array( 'option1' => 'value1', 'option2' => 'value2' ),
                'filter2 name' => TRUE,   // use filter2, no option.
                'filter3 name' => FALSE,  // do not use filter3
                'filter4 name' => 'trim', // use function trim as filter4
                'filter5 name' => function( $val ){}, // use function. 
                'mbJaKana'     => TRUE, 
                'err_msg'      => 'error message here',
                ),
        */
        // filters for email type.
        'asis'  => array(
              'trim'        => FALSE,
            ),
        'text'  => array(
            ),
        'mail'  => 
            array(
                'mbConvert'  => 'hankaku',
                'sanitize'   => FILTER_SANITIZE_EMAIL,
                'string'     => 'lower',
                'required'   => FALSE,
                'default'    => FALSE,
                'checkMail'  => TRUE,
                'err_msg'    => 'not a valid email format',
                ),
        'number'  =>
            array(
                'mbConvert'   => 'hankaku',
                'mbCheckKana' => 'hankaku_only',
                'number'      => 'number',
                'err_msg'     => 'enter a number',
            ),
        'int'  =>
            array(
                'mbConvert'   => 'hankaku',
                'number'      => 'int',
                'err_msg'     => 'enter an integer',
            ),
        'float'  =>
            array(
                'mbConvert'   => 'hankaku',
                'number'      => 'float',
                'err_msg'     => 'enter a float value',
            ),
        'date' =>
            array(
                'multiple'  => array( 'suffix' => array( 'y', 'm', 'd' ), 
                                      'connector' => '-' 
                                    ),
                'mbConvert' => 'hankaku',
                'datetype'  => 'ymd',
                'checkdate' => TRUE,
                'err_msg'   => 'enter a valid date',
            ),
        'ym' =>
            array(
                'multiple'  => array( 'suffix' => array( 'y', 'm' ),
                                      'connector' => '-' 
                                    ),
                'mbConvert' => 'hankaku',
                'datetype'  => 'ym',
                'err_msg'   => 'enter a year-month as YYYY-MM',
            ),
        'time' =>
            array(
                'multiple'  => array( 'suffix' => array( 'h', 'i', 's' ), 
                                      'connector' => ':' 
                                    ),
                'mbConvert' => 'hankaku',
                'datetype'  => 'His',
                'err_msg'   => 'enter a time',
            ),
        'datetime' =>
            array(
                'multiple'  => array( 'suffix'  => array( 'y', 'm', 'd', 'h', 'i', 's' ), 
                                      'sformat' => '%04d-%02d-%02d %02d:%02d:%02d' 
                                    ),
                'mbConvert' => 'hankaku',
                'datetype'  => 'dt',
            ),
        );
    // +--------------------------------------------------------------- +
    /** create filters array for given type and optional filters.
     */
    function __init( $options=NULL ) {
    }
    // +--------------------------------------------------------------- +
    /** create filters array for given type and optional filters.
     */
    function _getFilter( $filter, $type ) {
        if( !isset( self::$filters[ $type ] ) ) {
            $type = 'asis';
        }
        $filter = array_merge( self::$filters[ $type ], $filter );
        return $filter;
    }
    // +--------------------------------------------------------------- +
    /** validate a value based on type. 
     *  filter-verify-value
     */
    function validate( &$value, $type='asis', $filter=array(), &$error=NULL ) {
        $filters = self::_getFilter( $filter, $type );
        return self::_validateValue( $value, $filters, $error );
    }
    // +--------------------------------------------------------------- +
    /** get a validated value in $data array. 
     *  
     * @param array $data     look for $name in $data array.
     * @param string $name    name of value in $data array.
     * @param mix &$value     value found. 
     *                        returns NULL if value is not found. 
     * @param string $type    specify type of value.
     * @param array $filter   specify extra filter and options.
     * @param mix &$error     returns error message if validation fails. 
     * @return boolean
     *        returns TRUE if value is validated. 
     *        returns FALSE validation fails. 
     */
    function find( $data, $name, &$value, $type='asis', $filter=array(), &$error=NULL ) {
        $value = self::_find( $data, $name, $filter, $type );
        if( $value === NULL ) {
            // validate against empty value.
            $result = self::_validateValue( '', $type, $filter, $error );
        }
        else {
            $result = self::_validateValue( $value, $type, $filter, $error );
        }
        return $result;
    }
    // +--------------------------------------------------------------- +
    /** get a validated value in $data array. 
     *  TODO: Make sure returns NULL if value is not set in $data, 
     *        and returns FALSE if validation fails. 
     */
    function get( $data, $name, $type='asis', $filter=array(), &$error=NULL ) {
        $filters = self::_getFilter( $filter, $type );
        $value = self::_find( $data, $name, $filters );
        if( !self::_validateValue( $value, $filters, $error ) ) {
            $value = FALSE;
        }
        return $value;
    }
    // +--------------------------------------------------------------- +
    /** get a validated value in $_REQUEST array. 
     */
    function request( $name, $type='asis', $options=array(), &$error=NULL, $data=NULL ) {
        if( $data === NULL ) $data = $_REQUEST;
        return Dio::get( $data, $name, $type, $options, $error );
    }
    // +--------------------------------------------------------------- +
    /** find a value $name in $data array. 
     *  if multiple option is set, get as multiple value. 
     * 
     * @param array  $data    finds value in this array. 
     * @param string $name    look for $data[ $name ].
     * @param array $filters  filter to use (multiple and same* filters)
     * @return mix 
     *            returns FALSE if value are not found, 
     *            returns DEFAULT_EMPTY_VALUE if value is not a string
     *            returns the found value
     */
    function _find( $data, $name, &$filters=FALSE, $type=FALSE ) {
        if( $type !== FALSE ) {
            $filters = self::_getFilter( $filters, $type );
        }
        if( isset( $data[ $name ] ) ) {
            // simplest case. 
            $value = $data[ $name ];
            if( !Util::isValue( $value ) ) $value = self::DEFAULT_EMPTY_VALUE;
        }
        else
        if( isset( $filters[ 'multiple' ] ) && $filters[ 'multiple' ] !== FALSE ) {
            // case to read such as Y-m-d in three different values. 
            $value = self::_multiple( $data, $name, $filters[ 'multiple' ] );
        }
        else {
            $value = FALSE;
        }
        if( isset( $filters[ 'samewith' ] ) && $filters[ 'samewith' ] !== FALSE ) {
            // compare with other inputs as specified by samewith. 
            $sub_filters = $filters;
            $sub_filters[ 'samewith' ] = FALSE;
            $sub_value = self::_find( $data, $sub_name, $sub_filters );
            if( $sub_value ) {
                $filters[ 'sameas' ] = $sub_value;
            }
            else {
                $filters[ 'sameempty' ] = TRUE;
            }
        }
        return $value;
    }
    // +--------------------------------------------------------------- +
    /** a special method to obtain multiple value from a data. 
     *  not to be used like other filters. 
     *  @return mix value found, FALSE if is not set. 
     */
    function _multiple( $data, $name, $option ) 
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
        $lists = array();
        $found = FALSE;
        foreach( $option[ 'suffix' ] as $sfx ) {
            $name_sfx = $name . $sep . $sfx;
            $val = Util::getValue( $data, $name_sfx, FALSE );
            if( $val !== FALSE ) {
                $lists[] = $data[ $name_sfx ];
                $found   = self::DEFAULT_EMPTY_VALUE;
            }
        }
        if( $found === FALSE ) {
            // keep $found as FALSE;
        }
        else
        if( isset( $option[ 'sformat' ] ) ) {
            $param = array_merge( array( $option[ 'sformat' ] ), $lists );
            $found = call_user_func_array( 'sprintf', $param );
        }
        else {
            $found = implode( $con, $lists );
        }
        if( WORDY ) echo "_multiple( \$data, $name, \$option ) => $found \n";
        return $found;
    }
    // +--------------------------------------------------------------- +
    /** validates a value given list of filters. 
     * 
     *  @param mix   $value     value to validate and filtered. 
     *  @param array $filters   filters to apply to the value. 
     *  @param mix   $error     fill in error message if validation fails. 
     *  @return boolean 
     *          TRUE if validation and all are successful.
     *          FALSE if validation fails. 
     */
    function _validateValue( &$value, $filters=array(), &$error ) 
    {
        // -----------------------------------
        // build filter list. 
        if( is_array( $filters ) ) {
            $filters = array_merge( 
                self::$default_filters, self::$default_verifies, $filters );
        }
        else {
            $filters = array_merge( 
                self::$default_filters, self::$default_verifies );
        }
        // -----------------------------------
        // filter/verify $value.
        $success = TRUE;
        if( !empty( $filters ) )
        foreach( $filters as $f_name => $option ) 
        {
            if( $option === FALSE     ) continue;
            if( $f_name == 'multiple' ) continue;
            if( $f_name == 'err_msg'  ) continue;
            $err_msg = self::_getErrMsg( $filters, $f_name );
            $success = self::_applyFilter( $value, $f_name, $option, $error, $err_msg, $loop );
            if( !$success ) break;
            if( $loop == 'break' ) break;
        }
        if( WORDY ) 
            echo ($success) ? 
                "<font color=blue>validated: '$value'</font><br />":
                "<font color=red>invalidated: '$value'</font><br />";
        return $success;
    }
    // +--------------------------------------------------------------- +
    /** determine error messages from filters/f_name/option. 
     */
    function _getErrMsg( $filters, $f_name ) 
    {
        // build $messages: 
        //   0       => global err_msg, 
        //  'f_name' => filter specific err_msg
        if( isset( $filters[ 'err_msg' ] ) && 
            isset( $filters[ 'err_msg' ][ $f_name ] ) ) {
            $err_msg = $filters[ 'err_msg' ][ $f_name ];
        }
        else
        if( isset( self::$default_err_msgs[ $f_name ] ) ) {
            $err_msg = self::$default_err_msgs[ $f_name ];
        }
        else
        if( isset( $filters[ 'err_msg' ] ) && 
            isset( $filters[ 'err_msg' ][0] ) ) {
            $err_msg = $filters[ 'err_msg' ][0];
        }
        else
        if( isset( $filters[ 'err_msg' ] ) && 
            !is_array( $filters[ 'err_msg' ] ) ) {
            $err_msg = $filters[ 'err_msg' ];
        }
        else {
            $err_msg = "invalid {$f_name}";
        }
        if( WORDY > 5 ) echo "errMsg: '{$err_msg}' for $f_name<br />";
        return $err_msg;
    }
    // +--------------------------------------------------------------- +
    /** apply filter ($f_name with $option) to $value. 
     * 
     * @param string $value    value to be filtered. 
     * @param string $f_name   name of filter. 
     * @param mix    $option   option for the filter. 
     * @param mix    $error    fills with $err_msg if filter fails. 
     * @param string $err_msg  specifies error message when filter fails. 
     * @param string $loop     optionaly sets to TRUE to break loop.
     * @return boolean         FALSE if filter fails, otherwise TRUE.  
     */ 
    function _applyFilter( &$value, $f_name, $option, &$error=NULL, $err_msg='err', &$loop=NULL ) 
    {
        if( WORDY > 5 ) {  echo "_applyFilter( '$value', $f_name, $option, $err_msg )<br/>"; };
        $success = TRUE;
        self::_getFilterFunc( $f_name, $option, $filter, $arg );
        if( WORDY > 3 ) {  echo "_applyFilter( '$value', $f_name, $arg, $err_msg )<br/>"; };
        // -----------------------------------
        // filter/verify value. 
        if( is_callable( $filter ) ) {
            $success = call_user_func_array( $filter, array( &$value, $arg, &$loop ) );
            if( WORDY > 5 ) echo "apply function, success=$success, value=$value <br />";
        }
        else
        if( is_callable( $option ) ) {
            $success = call_user_func_array( $option, array( &$value, $arg ) );
            if( WORDY > 5 ) echo "apply function, success=$success, value=$value <br />";
        }
        else {
            throw new \Exception( "$filter not found as standard way." );
        }
        if( !$success ) { // it's an error. set an error message in $error.
            if( $err_msg ) { // use err_msg in option. 
                $error = $err_msg;
            }
            else { // use generic error message. 
                $err_msg = "error@{$f_name}";
            }
            if( WORDY ) 
                echo "<font color=red>Dio::_applyFilter failed on '$value', " . 
                     "filter( $filter, $arg ) => $error</font><br/>\n";
            $success = FALSE;
        }
        else 
        if( $success !== TRUE ) {
            $success = TRUE;
            if( WORDY ) echo "filter $filter success but not TRUE\n";
        }
        return $success;
    }
    // +--------------------------------------------------------------- +
    /** get real function and argument for given f_name/option.
     */ 
    function _getFilterFunc( $f_name, $option, &$filter, &$arg ) 
    {
        if( WORDY > 5 ) {  echo "filter( $f_name )"; var_dump( $option ); };
        // -----------------------------------
        // determine real $filter from $f_name and $option. 
        $filter = $f_name;
        if( isset( self::$filter_options[ $f_name ] ) ) {
            // overwrite f_name with filter_options
            if( is_callable( self::$filter_options[ $f_name ] ) ) {
                // it's a string. use the name in the filter list. 
                $filter = self::$filter_options[ $f_name ];
            }
            else
            if( !is_array( self::$filter_options[ $f_name ] ) ) {
                $filter = self::$filter_options[ $f_name ];
            }
            else 
            if( isset( self::$filter_options[ $f_name ][0] ) ) {
                // found array info in the filter list.
                // the first item is always the filter name. 
                $filter = self::$filter_options[ $f_name ][0];
            }
        }
        // -----------------------------------
        // now, get the option. 
        $arg = $option;
        if(is_callable( $option ) ) {
            // do nothing. 
        }
        else
        if( isset( self::$filter_options[ $f_name ] ) && 
            is_array( self::$filter_options[ $f_name ] ) ) {
            // check for more options.
            if( isset( self::$filter_options[ $f_name ][ $option ] ) ) {
                // use predefined option. 
                $arg = self::$filter_options[ $f_name ][ $option ];
            }
            else 
            if( isset( self::$filter_options[ $f_name ][1] ) ) {
                // use option in filter_potions...
                $arg = self::$filter_options[ $f_name ][1];
            }
        }
        if( WORDY > 5 ) {  echo "_getFilterFunc( $f_name, $option, &$filter, &$arg )<br/>"; };
    }
    // +--------------------------------------------------------------- +
    //  modify filter settings.
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
}



?>