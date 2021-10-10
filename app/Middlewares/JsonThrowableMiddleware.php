<?php

namespace Mimo\Middlewares;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *@see https://www.php-fig.org/psr/psr-15/
 */
final class JsonThrowableMiddleware implements MiddlewareInterface
{
    /**
     * @param  ServerRequestInterface  $request PSR-7 request
     * @param  RequestHandlerInterface $handler PSR-15 request handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $t) {
            return new Response(
                status: $t->getCode(),
                body: json_encode([
                    'error' => true,
                    'description' => match (get_class($t)) {
                        default => $t->getMessage()
                    },
                ])
            );
        }
    }
}
