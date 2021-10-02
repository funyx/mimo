<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$container = new ContainerBuilder();

$container->addDefinitions(require_once __DIR__.'/container.php');

try {
	$app = Bridge::create($container->build());
} catch (Exception $e) {
	throw new RuntimeException('DI\Container build failed');
}

$app->get('/', function ( ServerRequestInterface $request, ResponseInterface $response ): ResponseInterface
{
	$message = array_key_exists('name', $request->getQueryParams()) ? strlen($request->getQueryParams()['name']) ? 'Hello '.$request->getQueryParams()['name'] : '"name" is empty string' : 'Hello World';
	$response->getBody()->write($message);

	return $response;
});

if ( !array_key_exists('app', $_SERVER)) {
	$_SERVER['app'] = $app;
}

return $app;
