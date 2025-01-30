<?php

namespace Tests\Feature\Links;

use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateLinksTest extends TestCase
{
    protected $method = Request::METHOD_POST;
    protected string $uri = '/links';

    public function testShouldResponseWithHttpOk()
    {
        $this->defaultTest(
            $this->method,
            $this->uri,
            data: [
                'name' => 'Test Link',
                'description' => '???text???',
                'origin' => 'https://ya.ru/',
                'group_id' => [1]
            ],
        );

        $this->assertDatabaseHas('links', ['name' => 'Test Link']);
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
                'description' => 1,
                'origin' => 123,
                'group_id' => [1]
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
        $user->givePermissionTo('link--create');

        $this->defaultTest(
            $this->method,
            $this->uri,
            data: [
                'name' => 'Test Link',
                'description' => '???text???',
                'origin' => 'https://ya.ru/'
            ],
            user: $user
        );

        $this->assertDatabaseHas('links', ['name' => 'Test Link']);
    }

    public function testShouldResponseWithHttpForbiddenIfDontHavePermissions()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);

        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_FORBIDDEN,
            data: [
                'name' => 'Test Link',
                'description' => '???text???',
                'origin' => 'https://ya.ru/',
                'group_id' => [1]
            ],
            user: $user
        );
    }

    public function testShouldResponseWithHttpForbiddenIfNotGroupOwner()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('link--create');

        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_FORBIDDEN,
            data: [
                'name' => 'Test Link',
                'description' => '???text???',
                'origin' => 'https://ya.ru/',
                'group_id' => [1]
            ],
            user: $user
        );
    }

    public function testShouldResponseWithHttpForbiddenIfLinksInGroupLimitReached()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('link--create');

        $group = $user->groups->first();
        $group->count = config('constants.env.links_per_group');
        $group->update();

        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_FORBIDDEN,
            data: [
                'name' => 'Test Link',
                'description' => '???text???',
                'origin' => 'https://ya.ru/',
            ],
            user: $user
        );
    }
}
