<?php
/**
 * CURL helper
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2018
 */
namespace VolumNet\CURL;

use \phpQueryObject;
use \phpQuery;

/**
 * CURL helper class
 * @property-read resource $curl Curl resource
 * @property string $cookieFile Cookie file path
 * @property int $timeout Timeout in seconds
 * @property string $userAgent User agent string
 */
class CURL
{
    /**
     * Default user agent string
     */
    const DEFAULTUSERAGENT = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36';

    /**
     * Curl resource
     * @var resource
     */
    protected $_curl = null;

    /**
     * Cookie file path
     * @var string
     */
    protected $_cookieFile = null;

    /**
     * Timeout in seconds
     * @var int
     */
    protected $_timeout = null;

    /**
     * User agent string
     * @var string
     */
    protected $_userAgent = '';

    /**
     * Variable getter
     * @param string $var Variable name
     */
    public function __get($var)
    {
        switch ($var) {
            case 'curl':
            case 'cookieFile':
            case 'timeout':
            case 'userAgent':
                return $this->{'_' . $var};
                break;
        }
    }


    /**
     * Variable setter
     * @param string $var Variable name
     * @param mixed $val Variable value
     */
    public function __set($var, $val)
    {
        switch ($var) {
            case 'timeout':
                $this->_timeout = (int)$val;
                curl_setopt($this->_curl, CURLOPT_TIMEOUT, $timeout);
                break;
            case 'userAgent':
                $this->_userAgent = trim($val);
                curl_setopt($this->_curl, CURLOPT_USERAGENT, $this->_userAgent);
                break;
            case 'cookieFile':
                $this->_cookieFile = trim($val);
                curl_setopt($this->_curl, CURLOPT_COOKIEJAR, $this->_cookieFile); // сохранять куки в файл
                curl_setopt($this->_curl, CURLOPT_COOKIEFILE, $this->_cookieFile);
                break;
        }
    }


    /**
     * Class constructor
     * @param int $timeout Timeout in seconds
     * @param string $userAgent User agent string
     * @param string $cookieFile Cookie file path
     */
    public function __construct($timeout = 30, $userAgent = null, $cookieFile = '')
    {
        $this->_curl = curl_init();
        $this->userAgent = $userAgent ?: static::DEFAULTUSERAGENT;
        $this->timeout = $timeout;
        curl_setopt(
            $this->_curl,
            CURLOPT_HTTPHEADER,
            array(
                'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language:ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
                'Cache-Control:max-age=0',
                'Connection:keep-alive',
            )
        );
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, 1);
        if ($cookieFile) {
            $this->cookieFile = $cookieFile;
        }
    }


    /**
     * Connects to URL
     * @param string $url URL to connect
     * @param array $data POST-data (if empty, GET protocol will be used)
     * @param boolean $withHeaders return both headers and response text in array
     * @param 'text'|'json'|'jsonObject'|'jsonArray'|'phpquery' $outputFormat Output format
     * @return string|array(array<string> $headers, mixed $response) Response text or array of headers' array and response text
     */
    public function getURL($url, array $data = array(), $withHeaders = false, $outputFormat = 'text')
    {
        curl_setopt($this->_curl, CURLOPT_URL, $url);
        curl_setopt($this->_curl, CURLOPT_HEADER, $withHeaders);
        if (parse_url($url, PHP_URL_SCHEME) == 'https') {
            curl_setopt($this->_curl, CURLOPT_CAINFO, __DIR__ . '/../cacert.pem');
        }
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($this->_curl, CURLOPT_SAFE_UPLOAD, false);
        }
        if ($data) {
            curl_setopt($this->_curl, CURLOPT_POST, true);
            // curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->_curl, CURLOPT_POSTFIELDS, http_build_query($data));
        } else {
            curl_setopt($this->_curl, CURLOPT_POST, false);
        }
        $result = curl_exec($this->_curl);
        if ($withHeaders) {
            $header_size = curl_getinfo($this->_curl, CURLINFO_HEADER_SIZE);
            $body = substr($result, $header_size);
            $h = substr($result, 0, $header_size);
            $headers = array();
            $h = explode("\n", $h);
            foreach ($h as $val) {
                $val = explode(':', $val, 2);
                $headers[trim($val[0])] = trim($val[1]);
            }
        } else {
            $body = $result;
        }

        switch (mb_strtolower($outputFormat)) {
            case 'json':
            case 'jsonobject':
                $body = json_decode($body, false);
                break;
            case 'jsonarray':
                $body = json_decode($body, true);
                break;
            case 'phpquery':
                $body = phpQuery::newDocument($body);
                break;
        }

        if ($withHeaders) {
            return array($headers, $body);
        } else {
            return $body;
        }
    }
}
