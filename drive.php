<?php
mb_internal_encoding( 'UTF-8' );
error_reporting( E_ALL );
define( 'WORDY', 4 );
require_once( "./Dio.php" );

use CenaDTA\Dio\Dio as Dio;

?>
<!DOCTYPE html>
<html lang='ja'>
<head>
<meta charset='UTF-8' />
</head>
<body>
<?php

$input  = 'a text';
$return = Dio::verify( $input, 'text', array(), $error );

echo "<br />test #1 <br />";

$input  = 'a text';
$return = Dio::filter( $input, 'upper', TRUE, $error );

echo "<br />test #2 <br />";

$input  = 'a text';
$return = Dio::filter( $input, 'pattern', '[ a-z]*', $error );

$return = Dio::filter( $input, 'pattern', '[A-Z]*', $error, 'only upper case' );

echo "<br />test #3 <br />";

$input  = 'a text';
$return = Dio::verify( $input, 'asis', array(), $error );

$input  = "a \ntext";
$return = Dio::verify( $input, 'text', array(), $error );

$input  = 'boGus@eXample.com';
$return = Dio::verify( $input, 'mail', array(), $error );

$input  = 'a text';
$return = Dio::verify( $input, 'mail', array(), $error );

echo "<br />test #4 <br />";

$input  = '100';
$return = Dio::verify( $input, 'number', array(), $error );

$input  = '１００';
$return = Dio::verify( $input, 'number', array(), $error );

$input  = '-100.0';
$return = Dio::verify( $input, 'number', array(), $error );

$input  = '-100.0';
$return = Dio::verify( $input, 'float', array(), $error );

echo "<br />test #5 <br />";

$input  = 'same as';
$return = Dio::filter( $input, 'sameas', 'same as', $error );
$return = Dio::filter( $input, 'sameas', 'different', $error );

$return = Dio::verify( $input, 'asis', array(
    'sameas' => 'same as'
), $error );
$return = Dio::verify( $input, 'asis', array(
    'sameas' => 'different'
), $error );


?>
</body>
</html>