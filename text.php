<?php






PGG::get(     $_REQUEST, 'email', PGG::EMAIL );
PGG::getMail( $_REQUEST, 'email' );

$filters[ PGG::EMAIL ] = array(
	array( FILTER_BASIC, array( 'charset' => 'UTF-8' ) ),
	array( FILTER_JA_HANKAKU ),     // 
	array( FILTER_TOLOWER ),        // 
	array( FILTER_SANITIZE_EMAIL ), // FILTER_VALIDATE_EMAIL
	array( FILTER_DEFAULT ),        // 
);
$verifies[ PGG::EMAIL ] = array(
	array( VERIFY_REQUIRED ),    // 
	array( VERIFY_EMAIL ),       // FILTER_SANITIZE_EMAIL
	array( VERIFY_REGEXP ),      // 
);

function getEmail( $data, $name, $options=array() ) {
	get( $data, $name, PGG::EMAIL, $options );
}

function get( $src_array, $name, $type, $options=array() ) {
	if( !isset( $src_array[ $name ] ) ) {
		return FALSE;
	}
	$value = $data[ $name ];
	if( !have_value( $value ) && isset( $options[ 'default' ] ) ) {
		return $options[ 'default' ];
	}
	if( !fvVal( $type, $error, $value, $options ) ) {
		$value = FALSE;
	}
	return $value;
}

// filter-verify-value
function fvVal( &$value, &$error, $type, $options=array() ) {
	if( is_array( $value ) ) {
		foreach( $value as $key => $val ) {
			fvVal( $value[ $key ], $error, $type, $options );
		}
	}
	if( !empty( $filters[ $type ] )
	foreach( $filters[ $type ] as $info ) {
		$filter_name = $info[0];
		$filter_opt  = $info[1];
		$options     = array_merge( $filter_opt, $options );
		$value       = Filter::filter( $value, $filter_name, $options );
	}
	$success = TRUE;
	if( !empty( $verifies[ $type ] )
	foreach( $verifies[ $type ], $info ) {
		$verify_name = $info[0];
		$verify_opt  = $info[1];
		$options     = array_merge( $verify_opt, $options );
		$success &= Verify::verify( $value, $error, $verify_name, $options );
	}
	return $success;
}

function verify( $value, &$error, $info, $options ) {
}

?>