<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$container = new ContainerBuilder();
$container->addDefinitions(__DIR__.'/container.php');

try {
    $app = Bridge::create($container->build());
} catch (Exception $e) {
    throw new RuntimeException('DI\Container build failed');
}

// COMMAND ANCHOR : DO NOT TOUCH!!!

if (! array_key_exists('app', $_SERVER)) {
    $_SERVER['app'] = $app;
}

return $app;
