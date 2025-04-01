<?php

namespace Tests\Feature\Auth;

use App\Modules\Auth\Events\PasswordResetting;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    protected $method = Request::METHOD_POST;
    protected string $uri = '/auth/forgot-password';

    public function testShouldResponseWithHttpOk()
    {
        Event::fake();

        User::factory()->create(['email' => 'test@test.com']);

        $this->defaultTest(
            Request::METHOD_POST,
            $this->uri,
            data: [
                'email' => 'test@test.com',
            ],
            needAuth: false,
        );

        Event::assertDispatched(PasswordResetting::class);
    }

    public function testShouldResponseWithHttpUnprocessableIfNoParams()
    {
        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            needAuth: false,
        );
    }

    public function testShouldResponseWithHttpUnprocessableIfInvalidParams()
    {
        $this->defaultTest(
            $this->method,
            $this->uri,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            data: [
                'email' => 'fsafas.com',
            ],
            needAuth: false,
        );
    }
}
