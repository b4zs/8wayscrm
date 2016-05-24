<?php

define('DEBUG', false);
//define('CACHE', __DIR__.'/redmine-proxy-cache');
define('CACHE', false);

function buildRequestHeaders()
{
	$headers = getallheaders();
	$curlHeaders = array();
	foreach ($headers as $k => $v) {
		if ($k === 'Host') continue;
		$curlHeaders[] = "$k: $v";
	}
	return $curlHeaders;
}


function executeRequest($method, $url, $body, $curlHeaders)
{
	//var_dump($curlHeaders);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);

	if ('POST' === $method) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_POST, 1);
	}

	$response = curl_exec($ch);
    if ($error = curl_error($ch)) {
        throw new \RuntimeException($error);
    }

	return array($ch, $response);
}

function parseCurlResponse($ch, $response)
{
// Then, after your curl_exec call:
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$curlResponseHeaders = substr($response, 0, $header_size);
	$body = substr($response, $header_size);

	$responseHeaders = array();
	$curlResponseHeaders = array_filter(explode(PHP_EOL, $curlResponseHeaders));
	foreach ($curlResponseHeaders as $rawResponseHeader) {
		$rawResponseHeader = trim($rawResponseHeader);
		if (!$rawResponseHeader) continue;
		$h = explode(':', $rawResponseHeader);
		if (in_array($h[0], array('Transfer-Encoding'))) {
			continue;
		}
		$responseHeaders[$h[0]] = isset($h[1]) ? $h[1] : null;
	}
	return array($body, $responseHeaders);
}

function sendResponse($responseHeaders, $body)
{
	foreach ($responseHeaders as $key => $value) {
		header("$key: $value");
	}

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin, Accept, Authorization, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers');
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT');

	echo $body;
}

if (!function_exists('getallheaders')) {
	function getallheaders() {
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}


$proxyRoot = 'http://redmine.assist01.gbart.h3.hu';
//$proxyRoot = 'http://localhost:3000';

$url = $proxyRoot.str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);
$requestHeaders = buildRequestHeaders();
$method = $_SERVER['REQUEST_METHOD'];
$requestBody = file_get_contents('php://input');

function processRequest($request)
{
	list($method, $url, $requestHeaders, $requestBody) = $request;
	list($ch, $response) = executeRequest($method, $url, $requestBody, $requestHeaders);
	list($responseBody, $responseHeaders) = parseCurlResponse($ch, $response);

	return array($responseHeaders, $responseBody);
}

$request = array($method, $url, $requestHeaders, $requestBody);
$response = null;

if ('OPTIONS' === $method) { // CORS
	sendResponse(array(), '');
	exit(0);
}

$shouldCache = CACHE 
	&& ($method === 'GET')
	;

if (CACHE && $shouldCache) {
	$cacheKey = md5(serialize($request));
	$cacheHit = false;

	$cacheFilename = CACHE.'/'.$cacheKey.'.txt';
	if (!is_dir(CACHE)) mkdir(CACHE);
	if (file_exists($cacheFilename)) {
		$cacheHit = true;
		$response = unserialize(file_get_contents($cacheFilename));
	}
}
if (null == $response) {
	$response = processRequest($request);
	if (CACHE && $shouldCache) {
		file_put_contents($cacheFilename, serialize($response));
	}
}

list ($responseHeaders, $responseBody) = $response;
$responseHeaders['debug-using-cache'] = json_encode($shouldCache);
if ($shouldCache) {
	$responseHeaders['debug-cache-hit'] = json_encode($cacheHit);
}

//
//if (DEBUG) {
//	var_dump($requestHeaders, $url, $response, $responseHeaders, $body);
//	die;
//}
sendResponse($responseHeaders, $responseBody);
