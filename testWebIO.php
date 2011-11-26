<?php
use CenaDTA\Util\WebIO as WebIO;
error_reporting( E_ALL );
require_once( dirname( __FILE__ ) . "/Util.php" );
require_once( dirname( __FILE__ ) . "/Web.php" );
define( 'WORDY', 0 );

/**
 * PHPUnit test for Web Input/Output object.
 */
class Util_DataIOTest extends PHPUnit_Framework_TestCase
{
	// +----------------------------------------------------------------------+
	public function setUp()
	{
	}
	// +----------------------------------------------------------------------+
	public function test_WebIO()
	{
        $data = array(
            'test' => 'test',
            'more' => 'more',
        );
        $encoded = WebIO::encodeData( $data, WebIO::ENCODE_NONE );
        $this->assertEquals( serialize( $data ), $encoded );
        $decoded = WebIO::decodeData( $encoded, WebIO::ENCODE_NONE );
        $this->assertEquals( $data, $decoded );
        
        $encoded = WebIO::encodeData( $data, WebIO::ENCODE_BASE64 );
        $decoded = WebIO::decodeData( $encoded, WebIO::ENCODE_BASE64 );
        $this->assertEquals( $data, $decoded );
        
        $encoded = WebIO::encodeData( $data, WebIO::ENCODE_CRYPT );
        $decoded = WebIO::decodeData( $encoded, WebIO::ENCODE_CRYPT );
        $this->assertEquals( $data, $decoded );
        
    }
	// +----------------------------------------------------------------------+
}

?>
