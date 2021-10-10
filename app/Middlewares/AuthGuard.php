<?php

namespace Mimo\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *@see https://www.php-fig.org/psr/psr-15/
 */
final class AuthGuard implements MiddlewareInterface
{
    public function __construct(private string $strategy, private array $options = [])
    {
    }

    /**
     * @param  ServerRequestInterface  $request PSR-7 request
     * @param  RequestHandlerInterface $handler PSR-15 request handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($provider = $request->getAttribute($this->strategy)) {
            // TODO check scopes
            return $handler->handle($request);
        } else {
            throw new \RuntimeException('Unauthorized', 401);
        }
    }
}
