<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Mimo\Actions\PreflightAction;
use Mimo\Middlewares\AuthGuard;
use Mimo\Middlewares\AuthStrategiesMiddleware;
use Mimo\Middlewares\CorsMiddleware;
use Mimo\Middlewares\JsonThrowableMiddleware;
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
    $app->add($container->get(JsonThrowableMiddleware::class)); // <-- last
    $app->add($container->get(AuthStrategiesMiddleware::class));
    $app->add($container->get(CorsMiddleware::class));
    $app->add($container->get(RoutingMiddleware::class)); // <-- first
} catch (DependencyException $e) {
    throw new RuntimeException('DI\Container DependencyException : '.$e->getMessage());
} catch (NotFoundException $e) {
    throw new RuntimeException('DI\Container NotFoundException : '.$e->getMessage());
}

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $body = ['message' => 'Hello World'];
    $response->getBody()->write(json_encode($body));

    return $response->withHeader('Content-Type', 'application/json');
});
$app->options('/', PreflightAction::class);

$app->get('/cognito', function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $body = ['message' => 'Hello World'];
    $response->getBody()->write(json_encode($body));

    return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthGuard('cognito'));
$app->options('/cognito', PreflightAction::class);
// COMMAND ANCHOR : DO NOT TOUCH!!!

if (! array_key_exists('app', $_SERVER)) {
    $_SERVER['app'] = $app;
}

return $app;
