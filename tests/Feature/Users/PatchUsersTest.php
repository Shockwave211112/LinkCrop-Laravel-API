<?php

namespace Tests\Feature\Users;

use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class PatchUsersTest extends TestCase
{
    protected $method = Request::METHOD_PATCH;
    protected string $uri = '/user/';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::first();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id,
            data: [
                'email' => 'test@test.com',
            ]
        );

        $this->assertDatabaseHas('users', ['email' => 'test@test.com']);
    }

    public function testShouldResponseWithHttpOkIfNoParams()
    {
        $user = User::first();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id
        );
    }

    public function testShouldResponseWithHttpUnprocessableIfInvalidParams()
    {
        $user = User::first();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            data: [
                'email' => 'fsafas.com',
                'name' => 1,
                'password' => '222',
                'role_id' => [99999]
            ]
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
