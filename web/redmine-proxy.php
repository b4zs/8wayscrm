<?php

define('DEBUG', false);

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
	curl_setopt($ch, CURLOPT_TIMEOUT, 2);

	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);

	if ('POST' === $method) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_POST, 1);
	}

	$response = curl_exec($ch);
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

	echo $body;
}

$url = 'http://redmine.assist01.gbart.h3.hu'.str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);
$requestHeaders = buildRequestHeaders();
$method = $_SERVER['REQUEST_METHOD'];
$requestBody = file_get_contents('php://input');
list($ch, $response) = executeRequest($method, $url, $requestBody, $requestHeaders);
list($body, $responseHeaders) = parseCurlResponse($ch, $response);

if (DEBUG) {
	var_dump($requestHeaders, $url, $response, $responseHeaders, $body);
	die;
}
sendResponse($responseHeaders, $body);
