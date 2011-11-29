<?php
use CenaDTA\Util\WebIO as WebIO;
error_reporting( E_ALL );
require_once( dirname( __FILE__ ) . "/Util.php" );
require_once( dirname( __FILE__ ) . "/Web.php" );
define( 'WORDY', 0 );

/**
 * PHPUnit test for Web Input/Output object.
 */
class Util_WebIOTest extends PHPUnit_Framework_TestCase
{
	// +----------------------------------------------------------------------+
	public function setUp()
	{
	}
	// +----------------------------------------------------------------------+
	public function test_saveLoadPost()
	{
        $data = array(
            'test' => 'test',
            'more' => 'more',
        );
        $encode_type = WebIO::ENCODE_NONE;
        $save_id     = WebIO::$save_id;
        
        $encoded     = WebIO::encodeData( $data, $encode_type );
        $enc_id      = WebIO::$encode_id . $save_id;
        
        // test savePost generates to html hidden tag
        $html = WebIO::savePost( $data, NULL, $encode_type );
        list( $html1, $html2 ) = explode( '><', $html );
        
        $this->assertContains( $save_id, $html1 );
        $this->assertContains( $encoded, $html1 );
        $this->assertContains( $enc_id,  $html2 );
        
        // test saveSession saves to $_SESSION. 
        $html = WebIO::saveSession( $data, NULL, $encode_type );
        $this->assertTrue( !empty( $_SESSION[ $save_id] ) );
        $this->assertEquals( $_SESSION[ $save_id], $encoded );
	}
	// +----------------------------------------------------------------------+
	public function test_en_de_code()
	{
        $data = array(
            'test' => 'test',
            'more' => 'more',
        );
        // test encode=none (just serialized data).
        $encoded = WebIO::encodeData( $data, WebIO::ENCODE_NONE );
        $this->assertEquals( serialize( $data ), $encoded );
        $decoded = WebIO::decodeData( $encoded, WebIO::ENCODE_NONE );
        $this->assertEquals( $data, $decoded );
        
        // test encode=base64
        $encoded = WebIO::encodeData( $data, WebIO::ENCODE_BASE64 );
        $decoded = WebIO::decodeData( $encoded, WebIO::ENCODE_BASE64 );
        $this->assertEquals( $data, $decoded );
        
        // test encode=crypt
        $encoded = WebIO::encodeData( $data, WebIO::ENCODE_CRYPT );
        $decoded = WebIO::decodeData( $encoded, WebIO::ENCODE_CRYPT );
        $this->assertEquals( $data, $decoded );
        
    }
	// +----------------------------------------------------------------------+
}

?>
