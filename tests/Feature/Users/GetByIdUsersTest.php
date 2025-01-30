<?php

namespace Tests\Feature\Users;

use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class GetByIdUsersTest extends TestCase
{
    protected $method = Request::METHOD_GET;
    protected string $uri = '/user/';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::first();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id,
        )->assertJsonFragment(['email' => $user->email]);
    }

    public function testShouldResponseWithHttpNotFound()
    {
        $this->defaultTest(
            $this->method,
            $this->uri . 9999999,
            Response::HTTP_NOT_FOUND,
        );
    }

    public function testShouldResponseWithHttpUnauthIfWithoutToken()
    {
        $user = User::first();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id,
            Response::HTTP_UNAUTHORIZED,
            needAuth: false
        );
    }

    public function testShouldResponseWithHttpForbiddenIfNotAdmin()
    {
        $user = User::first();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id,
            Response::HTTP_FORBIDDEN,
            role: User::BASIC_USER,
        );
    }
}
