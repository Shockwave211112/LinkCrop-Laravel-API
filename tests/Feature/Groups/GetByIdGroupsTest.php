<?php

namespace Tests\Feature\Groups;

use App\Modules\Links\Models\Group;
use App\Modules\Links\Models\GroupUser;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class GetByIdGroupsTest extends TestCase
{
    protected $method = Request::METHOD_GET;
    protected string $uri = '/groups/';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('group--show');

        $group = Group::factory()->user($user->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            user: $user
        );
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
        $group = Group::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $group->id,
            Response::HTTP_FORBIDDEN,
            role: User::BASIC_USER,
        );
    }
}
