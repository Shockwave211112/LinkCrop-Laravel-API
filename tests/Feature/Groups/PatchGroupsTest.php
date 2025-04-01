<?php

namespace Tests\Feature\Groups;

use App\Modules\Links\Models\Group;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class PatchGroupsTest extends TestCase
{
    protected $method = Request::METHOD_PATCH;
    protected string $uri = '/groups/';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--patch');

        $group = Group::factory()->user($user->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            data: [
                'name' => 'Test Group New'
            ],
            user: $user
        );

        $this->assertDatabaseHas('groups', ['name' => 'Test Group New']);
    }

    public function testShouldResponseWithHttpOkIfNoParams()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--patch');

        $group = Group::factory()->user($user->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            user: $user
        );

        $this->assertDatabaseHas('groups', ['name' => $group->name]);
    }

    public function testShouldResponseWithHttpUnprocessableIfInvalidParams()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--patch');

        $group = Group::factory()->user($user->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            data: [
                'name' => 1,
                'description' => 1,
                'count' => 'asfasfsa',
            ],
            user: $user
        );
    }

    public function testShouldResponseWithHttpUnauthIfWithoutToken()
    {
        $group = Group::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            Response::HTTP_UNAUTHORIZED,
            needAuth: false
        );
    }

    public function testShouldResponseWithHttpForbiddenIfNotOwner()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--create');
        $user->givePermissionTo('group--patch');

        $group = Group::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            Response::HTTP_FORBIDDEN,
            user: $user,
        );
    }
}
