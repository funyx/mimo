<?php

namespace Tests\Feature;

use Nyholm\Psr7\Factory\Psr17Factory;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    public function test_users_controller_can_list_mimo\models\users_records()
    {
        $response = $this->api('GET', '/');
        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->getHeaderLine('Content-Type'));
        $this->assertEquals('Hello World', $body);
    }

    public function test_users_controller_can_show_mimo\models\users_record()
    {
        $response = $this->api('GET', '/');
        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->getHeaderLine('Content-Type'));
        $this->assertEquals('Hello World', $body);
    }

    public function test_users_controller_can_store_mimo\models\users_record()
    {
        $response = $this->api('GET', '/');
        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->getHeaderLine('Content-Type'));
        $this->assertEquals('Hello World', $body);
    }

    public function test_users_controller_can_update_mimo\models\users_record()
    {
        $response = $this->api('GET', '/');
        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->getHeaderLine('Content-Type'));
        $this->assertEquals('Hello World', $body);
    }

    public function test_users_controller_can_destroy_mimo\models\users_record()
    {
        $response = $this->api('GET', '/');
        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->getHeaderLine('Content-Type'));
        $this->assertEquals('Hello World', $body);
    }
}
