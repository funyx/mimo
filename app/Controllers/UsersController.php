<?php

namespace Mimo\Controllers;

use Mimo\Controller;
use Mimo\Models\Users;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest as Request;

class UsersController extends Controller
{
    public function paginator(Response $response, Request $request) :Response
    {
        //
        return $response;
    }

    public function store(Response $response, Request $request) :Response
    {
        //
        return $response;
    }

    public function show(Response $response, Request $request, $id) :Response
    {
    	$m = new Users();
//        $m->where('id', $id);
//        $m->get();
        return $response;
    }

    public function update(Response $response, Request $request, $id) :Response
    {
        //
        return $response;
    }

    public function destroy(Response $response, Request $request, $id) :Response
    {
        //
        return $response;
    }
}
