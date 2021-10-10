<?php

use DI\Bridge\Slim\CallableResolver;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Middleware\RoutingMiddleware;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteParser;
use Slim\Routing\RouteResolver;
use function DI\autowire;
use function DI\create;
use function DI\get;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Mimo\Middlewares\CorsMiddleware;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

return [
    // psr 17 messages
    ResponseInterface::class => autowire(Psr17Factory::class),
    RequestFactoryInterface::class => autowire(Psr17Factory::class),
    ResponseFactoryInterface::class => autowire(Psr17Factory::class),
    ServerRequestFactoryInterface::class => autowire(Psr17Factory::class),
    StreamFactoryInterface::class => autowire(Psr17Factory::class),
    UploadedFileFactoryInterface::class => autowire(Psr17Factory::class),
    UriFactoryInterface::class => autowire(Psr17Factory::class),
    // psr 17 client
    ClientInterface::class => autowire(Client::class),
    // container /illuminate - port to laravel kernel/
    Manager::class => function () {
        $c = new Container();
        $c['config'] = fn () => new Repository(['database' => config('database')]);
        $m = new Manager($c);
        $m->bootEloquent();

        return $m;
    },
    'Mimo\Models\*' => function ($c, $target) {
        $model = new ($target->getName());

        return $model;
    },
	CallableResolverInterface::class => create(CallableResolver::class),
    RouteCollectorInterface::class => create(RouteCollector::class)
        ->constructor(
	        get(ResponseFactoryInterface::class),
			get(CallableResolverInterface::class),
        ),
    RouteResolverInterface::class => create(RouteResolver::class)
        ->constructor(
	        get(RouteCollectorInterface::class),
        ),
	RouteParserInterface::class => create(RouteParser::class)
        ->constructor(
	        get(RouteCollectorInterface::class),
        ),
    // psr 15 middlewares
    CorsMiddleware::class => create(CorsMiddleware::class),
    RoutingMiddleware::class => create(RoutingMiddleware::class)
	    ->constructor(
		    get(RouteResolverInterface::class),
		    get(RouteParserInterface::class),
	    ),
];
