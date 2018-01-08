# VolumNet implementation of CURL

## Installation

```
composer require volumnet/curl
```

## Default params

By default VolumNet CURL uses the following params:

- Request timeout: 30s
- User agent string: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36
- Cookie file: not used

## Usage

```
/**
 * @param int $timeout Timeout in seconds
 * @param string $userAgent User agent string
 * @param string $cookieFile Cookie file path
 */ 
$curl = new CURL(10, 'My User Agent', 'cookie.txt');

$curl->timeout = 25;
$curl->userAgent = 'Their User Agent';
$curl->cookieFile = 'cookie2.txt';

/**
 * Connects to URL
 * @param string $url URL to connect
 * @param array $data POST-data (if empty, GET protocol will be used)
 * @param boolean $withHeaders return both headers and response text in array
 * @param 'text'|'json'|'jsonObject'|'jsonArray'|'phpquery' $outputFormat Output format
 * @return string|array(array<string> $headers, mixed $response) Response text or array of headers' array and response text
 */
$result = $curl->getURL('http://httpbin.org/get?aaa=bbb', array(), true, 'jsonArray');

/**
 * Response:
 * Array
 * (
 *     [0] => Array
 *         (
 *             [HTTP/1.1 200 OK] =>
 *             [Connection] => keep-alive
 *             [Server] => meinheld/0.6.1
 *             [Date] => Mon, 08 Jan 2018 13:14:42 GMT
 *             [Content-Type] => application/json
 *             [Access-Control-Allow-Origin] => *
 *             [Access-Control-Allow-Credentials] => true
 *             [X-Powered-By] => Flask
 *             [X-Processed-Time] => 0.00164794921875
 *             [Content-Length] => 507
 *             [Via] => 1.1 vegur
 *             [] =>
 *         )
 *     [1] => Array
 *         (
 *             [args] => Array
 *                 (
 *                     [aaa] => bbb
 *                 )
 *             [headers] => Array
 *                 (
 *                     [Accept] => text/html,application/xhtml+xml,application/xml;q=0.9,image/webp;q=0.8
 *                     [Accept-Language] => ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4
 *                     [Cache-Control] => max-age=0
 *                     [Connection] => close
 *                     [Host] => httpbin.org
 *                     [User-Agent] => Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36
 *                 )
 *             [url] => http://httpbin.org/get?aaa=bbb
 *         )
 * )
 */

$result = $curl->getURL('https://httpbin.org/post?aaa=bbb', array('ccc' => 'ddd'), false, 'jsonObject');
/**
 * Response:
 * stdClass Object
 * (
 *     [args] => stdClass Object
 *         (
 *             [aaa] => bbb
 *         )
 *     [data] =>
 *     [files] => stdClass Object
 *         (
 *         )
 *     [form] => stdClass Object
 *         (
 *             [ccc] => ddd
 *         )
 *     [headers] => stdClass Object
 *         (
 *             [Accept] => text/html,application/xhtml+xml,application/xml;q=0.9,image/webp;q=0.8
 *             [Accept-Language] => ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4
 *             [Cache-Control] => max-age=0
 *             [Connection] => close
 *             [Content-Length] => 7
 *             [Content-Type] => application/x-www-form-urlencoded
 *             [Host] => httpbin.org
 *             [User-Agent] => Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36
 *         )
 *     [json] =>
 *     [url] => https://httpbin.org/post?aaa=bbb
 * )
 */
```