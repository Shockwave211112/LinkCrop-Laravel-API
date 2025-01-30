<?php

namespace Tests\Feature\Links;

use App\Modules\Links\Models\Link;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class PatchLinksTest extends TestCase
{
    protected $method = Request::METHOD_PATCH;
    protected string $uri = '/links/';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('link--patch');

        $link = Link::factory()->user($user->id)->group($user->groups->first()->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $link->id,
            data: [
                'name' => 'Test Link New'
            ],
            user: $user
        );

        $this->assertDatabaseHas('links', ['name' => 'Test Link New']);
    }

    public function testShouldResponseWithHttpOkIfNoParams()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('link--patch');

        $link = Link::factory()->user($user->id)->group($user->groups->first()->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $link->id,
            user: $user
        );

        $this->assertDatabaseHas('links', ['name' => $link->name]);
    }

    public function testShouldResponseWithHttpUnprocessableIfInvalidParams()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('link--patch');

        $link = Link::factory()->user($user->id)->group($user->groups->first()->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $link->id,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            data: [
                'name' => 1,
                'description' => 1,
                'referral' => [3],
                'origin' => 2,
            ],
            user: $user
        );
    }

    public function testShouldResponseWithHttpUnauthIfWithoutToken()
    {
        $link = Link::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $link->id,
            Response::HTTP_UNAUTHORIZED,
            needAuth: false
        );
    }

    public function testShouldResponseWithHttpForbiddenIfNotOwner()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('link--patch');

        $link = Link::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $link->id,
            Response::HTTP_FORBIDDEN,
            user: $user,
        );
    }
}
