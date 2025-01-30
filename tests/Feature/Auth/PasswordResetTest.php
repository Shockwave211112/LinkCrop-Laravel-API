<?php

namespace Tests\Feature\Auth;

use App\Modules\Auth\Events\PasswordResetting;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected $method = Request::METHOD_POST;
    protected string $uri = '/auth/change-password';

    public function testShouldResponseWithHttpOk()
    {
        Event::fake();

        User::factory()->create(['email' => 'test@test.com']);

        $this->defaultTest(
            $this->method,
            '/auth/forgot-password',
            data: [
                'email' => 'test@test.com',
            ],
            needAuth: false,
        );

        Event::assertDispatched(PasswordResetting::class);
        $resetUrl = Event::dispatched(PasswordResetting::class)[0][0]->url;

        $this->defaultTest(
            $this->method,
            $this->uri . '?' . parse_url($resetUrl)['query'],
            data: [
                'password' => '22222222',
                'password_confirmation' => '22222222'
            ],
            needAuth: false,
        );

        $this->defaultTest(
            $this->method,
            '/auth/login',
            data: [
                'email' => 'test@test.com',
                'password' => '22222222'
            ],
            needAuth: false,
        )->assertJsonStructure(['token']);
    }

    public function testShouldResponseWithHttpForbiddenIfExpired()
    {
        Event::fake();
        Carbon::setTestNow(Carbon::yesterday());

        User::factory()->create(['email' => 'test@test.com']);

        $this->defaultTest(
            $this->method,
            '/auth/forgot-password',
            data: [
                'email' => 'test@test.com',
            ],
            needAuth: false,
        );

        Event::assertDispatched(PasswordResetting::class);
        $resetUrl = Event::dispatched(PasswordResetting::class)[0][0]->url;

        Carbon::setTestNow();

        $this->defaultTest(
            $this->method,
            $this->uri . '?' . parse_url($resetUrl)['query'],
            Response::HTTP_FORBIDDEN,
            data: [
                'password' => '22222222',
                'password_confirmation' => '22222222'
            ],
            needAuth: false,
        );
    }

    public function testShouldResponseWithHttpUnprocessableIfNotBase64()
    {
        $this->defaultTest(
            $this->method,
            $this->uri . '?token=asfsafasfsa21312saft21',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            needAuth: false,
        );
    }

    public function testShouldResponseWithHttpForbiddenIfTokenNotFound()
    {
        $this->defaultTest(
            $this->method,
            $this->uri . '?token=ZXlKcGRpSTZJa3hKTjNSUGQycElOMk5pV25GVmRYZ3ZWVUpzTUZFOVBTSXNJblpoYkhWbElqb2lPVUpHVlhwUU9GTmFNR1F3TVZWV2FGTkhSaXR4YnpkM1drTnlkVTl0WlhkcFVrNTViR3hGVDFaSll6MGlMQ0p0WVdNaU9pSXhNMkZrT1Raa1lXTTVPVEV4TlRjMVlqbGxZekEzWm1VM04yTmtZakl5TW1Jd09UVmhObUprWkdSa05UZGhNVEprT0RJNE5ERTRabVU0T0RsaE1tVTJJaXdpZEdGbklqb2lJbjA9',
            Response::HTTP_FORBIDDEN,
            data: [
                'password' => '22222222',
                'password_confirmation' => '22222222'
            ],
            needAuth: false,
        );
    }
}
