<?php

namespace Tests\Feature\Links;

use App\Modules\Links\Models\Link;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class GetByIdLinksTest extends TestCase
{
    protected $method = Request::METHOD_GET;
    protected string $uri = '/links/';

    public function testShouldResponseWithHttpOk()
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        $user->givePermissionTo('link--show');

        $link = Link::factory()->user($user->id)->group($user->groups->first()->id)->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $link->id,
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
        $link = Link::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $link->id,
            Response::HTTP_FORBIDDEN,
            role: User::BASIC_USER,
        );
    }
}
