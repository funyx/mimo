<?php

namespace Mimo\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeAction
{
    public function __invoke(Response $response, Request $request): Response
    {
        $response->getBody()->write('Hello World!');
        return $response;
    }
}
