<?php

namespace Tests\Feature\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    protected $method = Request::METHOD_GET;
    protected string $uri = '/auth/logout';

    public function testShouldResponseWithHttpOk()
    {
        $this->defaultTest(
            $this->method,
            $this->uri,
        );
    }

    public function testShouldResponseWithHttpUnauthIfWithoutToken()
    {
        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_UNAUTHORIZED,
            needAuth: false
        );
    }
}
