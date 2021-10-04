<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

function getRoute($route_name, $params = []) {
	global $app;

	return $app->getContainer()->get( 'router' )->pathFor( $route_name, $params );
}

/**
 * @throws \Throwable
 */
function mockRequest($method, $uri, $data = null, $headers = null): ResponseInterface {
	global $app;

	$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

	$creator = new \Nyholm\Psr7Server\ServerRequestCreator(
		$psr17Factory, // ServerRequestFactory
		$psr17Factory, // UriFactory
		$psr17Factory, // UploadedFileFactory
		$psr17Factory  // StreamFactory
	);

	$serverRequest = $creator->fromGlobals();;

	$serverRequest = $serverRequest->withMethod($method);
	$serverRequest->withUri(new \Nyholm\Psr7\Uri($uri));

	if( isset( $headers ) && is_array($headers) ) {
		foreach($headers as $name => $value){
			$serverRequest = $serverRequest->withHeader($name, $value);
		}
	}

	if($data){
		$serverRequest->getBody()->write($data);
	}

	return $app->handle( $serverRequest );
}

function mockResponse(): ResponseInterface
{
	return new Response();
}

///**
// * @throws \Throwable
// */
//function get($uri, $data = null, $headers = null): ResponseInterface {
//	return api( 'GET', $uri, $data, $headers );
//}
//
///**
// * @throws \Throwable
// */
//function post($uri, $data = null, $headers = null): ResponseInterface {
//	return api( 'POST', $uri, $data, $headers );
//}
//
///**
// * @throws \Throwable
// */
//function put($uri, $data = null, $headers = null): ResponseInterface {
//	return api( 'PUT', $uri, $data, $headers );
//}
//
///**
// * @throws \Throwable
// */
//function delete($uri, $data = null, $headers = null): ResponseInterface {
//	return api( 'DELETE', $uri, $data, $headers );
//}
//
//function resJson(ResponseInterface $r, $associative = true){
//	$record = (string) $r->getBody();
//	return json_decode($record, $associative);
//}
