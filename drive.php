<?php
error_reporting( E_ALL );
define( 'WORDY', 6 );
require_once( "./Dio.php" );

use CenaDTA\Dio\Dio as Dio;

$input  = 'a text';
$return = Dio::filter( $input, 'upper', TRUE, $error );
echo $input;

?>
