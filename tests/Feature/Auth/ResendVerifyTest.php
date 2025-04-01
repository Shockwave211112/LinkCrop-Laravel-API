<?php

namespace Tests\Feature\Auth;

use App\Modules\Auth\Events\UserRegistered;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ResendVerifyTest extends TestCase
{
    protected $method = Request::METHOD_GET;
    protected string $uri = '/email/resend';

    public function testShouldResponseWithHttpOk()
    {
        Event::fake();

        $user = User::factory()->create(['email_verified_at' => null]);

        $this->defaultTest(
            $this->method,
            $this->uri,
            user: $user,
        );

        Event::assertDispatched(UserRegistered::class);
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

    public function testShouldResponseWithHttpForbiddenIfAlreadyVerified()
    {
        Event::fake();

        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_FORBIDDEN,
        );

        Event::assertNotDispatched(UserRegistered::class);
    }
}
