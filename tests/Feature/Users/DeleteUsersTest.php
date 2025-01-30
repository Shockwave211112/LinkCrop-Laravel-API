<?php

namespace Tests\Feature\Users;

use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class DeleteUsersTest extends TestCase
{
    protected $method = Request::METHOD_DELETE;
    protected string $uri = '/user/';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id,
        );

        $this->assertDatabaseMissing('users', ['email' => $user->email]);
    }

    public function testShouldResponseWithHttpNotFound()
    {
        $this->defaultTest(
            $this->method,
            $this->uri . 99999,
            Response::HTTP_NOT_FOUND,
        );
    }

    public function testShouldResponseWithHttpUnauthIfWithoutToken()
    {
        $user = User::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id,
            Response::HTTP_UNAUTHORIZED,
            needAuth: false
        );
    }

    public function testShouldResponseWithHttpForbiddenIfNotAdmin()
    {
        $user = User::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $user->id,
            Response::HTTP_FORBIDDEN,
            role: User::BASIC_USER,
        );
    }
}
