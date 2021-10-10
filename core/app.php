<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Mimo\Actions\PreflightAction;
use Mimo\Middlewares\CorsMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\RoutingMiddleware;

$container = new ContainerBuilder();
$container->addDefinitions(__DIR__.'/container.php');

try {
    $app = Bridge::create($container = $container->build());
} catch (Exception $e) {
    throw new RuntimeException('DI\Container build failed : '. $e->getMessage());
}

try {
	$app->add($container->get(CorsMiddleware::class));
	$app->add($container->get(RoutingMiddleware::class));
} catch (DependencyException $e) {
    throw new RuntimeException('DI\Container DependencyException : '.$e->getMessage());
} catch (NotFoundException $e) {
    throw new RuntimeException('DI\Container NotFoundException : '.$e->getMessage());
}

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $message = 'Hello World';
    $response->getBody()->write($message);

    return $response->withHeader('Content-Type', 'application/json');
});
$app->options('/', PreflightAction::class);
// COMMAND ANCHOR : DO NOT TOUCH!!!

if (! array_key_exists('app', $_SERVER)) {
    $_SERVER['app'] = $app;
}

return $app;
