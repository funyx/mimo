<?php

namespace Mimo\Actions;

use Mimo\Action;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PreflightAction extends Action
{
    /**
     * Handle the incoming request.
     *
     * @param  ResponseInterface        $response
     * @param  ServerRequestInterface   $request
     *
     * @return ResponseInterface
     */
    public function __invoke(ResponseInterface $response, ServerRequestInterface $request): ResponseInterface
    {
        //
        return $response;
    }
}
