<?php

namespace Mimo\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

/**
 *@see https://www.php-fig.org/psr/psr-15/
 */
final class CorsMiddleware implements MiddlewareInterface
{
    /**
     * @param  ServerRequestInterface  $request PSR-7 request
     * @param  RequestHandlerInterface $handler PSR-15 request handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $routingResults = $routeContext->getRoutingResults();
        $methods = $routingResults->getAllowedMethods();
        $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

        $response = $handler->handle($request);

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $methods))
            ->withHeader('Access-Control-Allow-Headers', $requestHeaders ?: '*');

        // Optional: Allow Ajax CORS requests with Authorization header
        return $response->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
