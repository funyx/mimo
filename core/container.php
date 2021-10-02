<?php

use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
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
	ResponseInterface::class             => DI\autowire(Psr17Factory::class),
	RequestFactoryInterface::class       => DI\autowire(Psr17Factory::class),
	ServerRequestFactoryInterface::class => DI\autowire(Psr17Factory::class),
	StreamFactoryInterface::class        => DI\autowire(Psr17Factory::class),
	UploadedFileFactoryInterface::class  => DI\autowire(Psr17Factory::class),
	UriFactoryInterface::class           => DI\autowire(Psr17Factory::class),
	// psr 17 client
	ClientInterface::class               => DI\autowire(Client::class),
	// db /eloquent/
	Manager::class                       => function ()
	{
		$c = new Container();
		$c->bind('config', fn() => new Repository(['database' => config('database')]));
		$m = new Manager($c);
		$m->setAsGlobal();
		$m->bootEloquent();
		return $m;
	},
];
