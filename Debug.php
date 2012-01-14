<?php

class Debug
{
    static $outputType = 'html';
    static $wordyLevel = 4;
    static function w1( $string ) {
        if( self::$wordyLevel < 1 ) return;
        self::put( $string );
    }
    static function w3( $string ) {
        if( self::$wordyLevel < 3 ) return;
        self::put( $string );
    }
    static function put( $string ) {
        echo $string . '<br />';
    }
    static function t1( $mix, $title=NULL ) {
        if( self::$wordyLevel < 1 ) return;
        self::arr( $mix, $title );
    }
    static function t3( $mix, $title=NULL ) {
        if( self::$wordyLevel < 3 ) return;
        self::arr( $mix, $title );
    }
    static function t5( $mix, $title=NULL ) {
        if( self::$wordyLevel < 5 ) return;
        self::arr( $mix, $title );
    }
    static function arr( $mix, $title=NULL ) {
        if( $title!== NULL ) echo "{$title}<br />";
        echo "<pre>";
        var_dump( $mix );
        echo '</pre>';
    }
}
