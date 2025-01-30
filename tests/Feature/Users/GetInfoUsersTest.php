<?php

namespace Tests\Feature\Users;

use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class GetInfoUsersTest extends TestCase
{
    protected $method = Request::METHOD_GET;
    protected string $uri = '/user/info';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::role(User::BASIC_USER)->first();

        $this->defaultTest(
            $this->method,
            $this->uri,
            user: $user
        )->assertJsonFragment(['email' => $user->email]);
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
