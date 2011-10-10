<?php
namespace CenaDta\Dio;
use CenaDta\Dio\Filter as Filter;
use CenaDta\Dio\Verify as Verify;

class Dio
{
	const DATA      = 'load';
	const POST      = 'Post';
	const GET       = 'Get';
	const SESSION   = 'Session';
	const COOKIE    = 'Cookie';
	/** order of data source to look for a named variables, $name. 
	 */
    static $find_order = array( self::DATA, self::POST, self::GET, self::SESSION ); // no self::COOKIE!
	/** general data storage from loadPost, loadSession, etc. 
	 */
	static $find_saved = array();
	// +--------------------------------------------------------------- +
	/**
	 * get data from post, get, or repositories as specified in find_order. 
	 * 
	 * @return mix
	 * 	returns found value. if no such name was set, returns FALSE;
	 */
	function & find( $name ) {
		$value = $this->findRaw( $name );
		$value = Filter::filter( $value, 'secure_filter' );
		return $value;
	}
	// +--------------------------------------------------------------- +
	/**
	 * get data from post, get, or repositories as specified in find_order. 
	 * 
	 * @return mix
	 * 	returns found value. if no such name was set, returns FALSE;
	 */
	function & findRaw( $name ) {
		static $pot_to_data = array();
		if( empty( $pot_to_data ) ) {
			$pot_to_data = array(
				self::DATA      => & static::$find_saved, 
				self::POST      => & $_POST, 
				self::GET       => & $_GET, 
				self::SESSION   => & $_SESSION, 
				self::COOKIE    => & $_COOKIE,
			);
		}
        if( WORDY > 3 ) echo "- <b>findValue</b>( $name )...";
		$this_wordy = 5;
        $val   = FALSE;
		$found = FALSE;
		
        if(  empty( static::$find_order ) || 
		    !is_array( static::$find_order ) ) { return NULL; }
		
		// now, find $name value in $pot.
		foreach( static::$find_order as $pot ) {
			if( WORDY > $this_wordy ) echo "var order: {$pot}<br>";
			if( !isset( $pot_to_data[ $pot ] ) ) {
				continue;
			}
			$data = $pot_to_data[ $pot ];
			$found    = & $this->findInData( $name, $data, $val );
			if( $found ) {
				$this->flag_VARIABLE_FOUND = $pot;
				break;
			}
		}
		if( WORDY > $this_wordy ) {
			wordy_table( $val, "findValue( $name ): found={$found} in {$this->flag_VARIABLE_FOUND}" );
		}
        return $val;
    }
	// +--------------------------------------------------------------- +
	/**
	 * searches for name in data array. 
	 *
	 * @param string $name
	 * 	name of data to search.
	 * @param array $data
	 * 	bunches of data which may contain 'name'
	 * @param mix $val
	 * 	found value. if not found set to FALSE. 
	 * @return bool    returns true if found. returns false if not. 
	 */
    function & findInData( $name, $data, &$val ) {
		if( WORDY > 4 ) echo "pgg_value::findInArray( $name, &$data, &$val )<br>\n";
        $found = FALSE;
		if( isset( $data[ $name ] ) ) {
			$found = TRUE;
			if( have_value( $data[ $name ] ) ) {
				$val = $data[ $name ];
			}
			else {
				$val   = ''; // '' means variable is set but has no value!
			}
			if( WORDY > 3 ) echo "found $name = '$val'...<br>\n";
		}
        return $found;
    }
	// +--------------------------------------------------------------- +
}


?>