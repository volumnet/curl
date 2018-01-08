<?php
namespace VolumNet\CURL;

use PHPUnit_Framework_TestCase;

/**
 * CURL Tester
 */
class CURLTest extends PHPUnit_Framework_TestCase
{
    public static function tearDownAfterClass()
    {
        @unlink('cookie.txt');
        @unlink('cookie2.txt');
    }

    /**
     * Tests getting/setting dynamic properties
     */
    public function testGetSet()
    {
        $curl = new CURL(10, 'My User Agent', 'cookie.txt');
        $this->assertTrue(is_resource($curl->curl));
        $this->assertEquals(10, $curl->timeout);
        $curl->timeout = 25;
        $this->assertEquals(25, $curl->timeout);
        $this->assertEquals('My User Agent', $curl->userAgent);
        $curl->userAgent = 'Their User Agent';
        $this->assertEquals('Their User Agent', $curl->userAgent);
        $this->assertEquals('cookie.txt', $curl->cookieFile);
        $curl->cookieFile = 'cookie2.txt';
        $this->assertEquals('cookie2.txt', $curl->cookieFile);
        $curl->cookieFile = 'cookie.txt';
        $this->assertNull($curl->abc);
    }


    /**
     * Tests getURL
     */
    public function testGetURL()
    {
        $curl = new CURL();

        $result = $curl->getURL('http://abc.def', array());
        $this->assertFalse($result);
        $result = $curl->getURL('http://httpbin.org/post', array(), true, 'phpquery');
        $this->assertEquals(405, curl_getinfo($curl->curl, CURLINFO_RESPONSE_CODE));
        $this->assertEquals('Method Not Allowed', $result[1]->find('h1')->html());

        $result = $curl->getURL('http://httpbin.org/get?aaa=bbb', array(), true, 'jsonArray');
        $this->assertRegExp('/Chrome/umi', $result[1]['headers']['User-Agent']);
        $this->assertEquals('bbb', $result[1]['args']['aaa']);

        $result = $curl->getURL('http://httpbin.org/get?aaa=bbb', array('ccc' => 'ddd'), true, 'json');
        $this->assertEquals(405, curl_getinfo($curl->curl, CURLINFO_RESPONSE_CODE));

        $result = $curl->getURL('https://httpbin.org/post?aaa=bbb', array('ccc' => 'ddd'), false, 'jsonObject');
        $this->assertEquals('bbb', $result->args->aaa);
        $this->assertEquals('ddd', $result->form->ccc);
        $this->assertEquals('https://httpbin.org/post?aaa=bbb', $result->url);
    }
}
