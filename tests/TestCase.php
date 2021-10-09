<?php

namespace Tests;

use League\OpenAPIValidation\PSR15\ValidationMiddlewareBuilder;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

class TestCase extends \PHPUnit\Framework\TestCase
{
    private App $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = app();

        $this->app->add((new ValidationMiddlewareBuilder())
            ->fromYamlFile(__DIR__ . '/../spec/openapi.yaml')
            ->getValidationMiddleware());
    }

    final protected function api(string $method, $uri, array $serverParams = []): ResponseInterface
    {
        try {
	        $requestFactory = $this->app->getContainer()->get(ServerRequestFactoryInterface::class);

	        $request = $requestFactory->createServerRequest($method, $uri, $serverParams);
            return $this->app->handle($request);
        } catch (ValidationFailed $e) {
            throw $e->getPrevious();
        }
    }
}
