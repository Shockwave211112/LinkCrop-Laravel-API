<?php

namespace Tests\Feature\Groups;

use App\Modules\Links\Models\Group;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class PutGroupsTest extends TestCase
{
    protected $method = Request::METHOD_PUT;
    protected string $uri = '/groups/';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--put');

        $group = Group::factory()->user($user->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            data: [
                'name' => 'Test Group New',
                'description' => 'New Desc',
                'count' => 0
            ],
            user: $user
        );

        $this->assertDatabaseHas('groups', ['name' => 'Test Group New']);
    }

    public function testShouldResponseWithHttpUnprocessableIfNoParams()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--put');

        $group = Group::factory()->user($user->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            user: $user
        );
    }

    public function testShouldResponseWithHttpUnprocessableIfInvalidParams()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--put');

        $group = Group::factory()->user($user->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            data: [
                'name' => ['Test Group'],
                'description' => 1
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
        $user->givePermissionTo('group--put');

        $group =  Group::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            Response::HTTP_FORBIDDEN,
            data: [
                'name' => 'Test Group New',
                'description' => 'New Desc',
                'count' => 0
            ],
            user: $user,
        );
    }
}
