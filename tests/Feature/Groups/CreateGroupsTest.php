<?php

namespace Tests\Feature\Groups;

use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateGroupsTest extends TestCase
{
    protected $method = Request::METHOD_POST;
    protected string $uri = '/groups';

    public function testShouldResponseWithHttpOk()
    {
        $this->defaultTest(
            $this->method,
            $this->uri,
            data: [
                'name' => 'Test Group',
                'description' => '???text???'
            ]
        );

        $this->assertDatabaseHas('groups', ['name' => 'Test Group']);
    }

    public function testShouldResponseWithHttpUnprocessableIfNoParams()
    {
        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    public function testShouldResponseWithHttpUnprocessableIfInvalidParams()
    {
        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            data: [
                'name' => ['f'],
                'description' => 1
            ]
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

    public function testShouldResponseWithHttpOkIfNotAdmin()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--create');

        $this->defaultTest(
            $this->method,
            $this->uri,
            data: [
                'name' => 'Test Group',
                'description' => '???text???'
            ],
            user: $user
        );

        $this->assertDatabaseHas('groups', ['name' => 'Test Group']);
    }

    public function testShouldResponseWithHttpForbiddenIfDontHavePermissions()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);

        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_FORBIDDEN,
            data: [
                'name' => 'Test Group',
                'description' => '???text???'
            ],
            user: $user
        );
    }
}
