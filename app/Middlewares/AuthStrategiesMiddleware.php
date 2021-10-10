<?php

namespace Mimo\Middlewares;

use Jose\Component\Core\JWKSet;
use Jose\Easy\Load;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 *@see https://www.php-fig.org/psr/psr-15/
 */
final class AuthStrategiesMiddleware implements MiddlewareInterface
{
    /**
     * @param  ServerRequestInterface  $request PSR-7 request
     * @param  RequestHandlerInterface $handler PSR-15 request handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = strlen($authorization = $request->getHeaderLine('Authorization'))
            ? $token = str_replace('Bearer ', '', $authorization)
            : false;

        $attributes = [
            'auth_strategies' => [],
            'cognito' => null,
        ];
        // signed JWT token ?
        if ($token && preg_match("/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/", $token)) {
            try {
                $cognito_iss = sprintf(
                    'https://cognito-idp.%s.amazonaws.com/%s',
                    config('app.cognito_region'),
                    config('app.cognito_pool_id'),
                );
                $jwt = explode('.', $token);
                $iss = json_decode(base64_decode($jwt[1]))->iss;
                // iss cognito pool ?
                if (
                    $cognito_iss === $iss
                    && $jwks = file_get_contents($cognito_iss.'/.well-known/jwks.json')
                ) {
                    $attributes['auth_strategies'][] = 'cognito';
                    $attributes['cognito'] = Load::jws($token)
                                              ->keyset(JWKSet::createFromJson($jwks))
                                              ->run();
                }
            } catch (\Exception $e) {
                throw new \RuntimeException('Auth Middleware : '. $e->getMessage());
            }
        }

        foreach ($attributes as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        return $handler->handle($request);
    }
}
