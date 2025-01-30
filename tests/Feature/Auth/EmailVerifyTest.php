<?php

namespace Tests\Feature\Auth;

use App\Modules\Auth\Events\UserRegistered;
use App\Modules\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EmailVerifyTest extends TestCase
{
    protected $method = Request::METHOD_GET;
    protected string $uri = '/email/verify';

    public function testShouldResponseWithHttpOk()
    {
        Event::fake();

        $this->defaultTest(
            Request::METHOD_POST,
            '/auth/registration',
            data: [
                'email' => 'test@test.com',
                'name' => 'Test',
                'password' => '11111111',
                'password_confirmation' => '11111111'
            ],
            needAuth: false,
        );

        Event::assertDispatched(UserRegistered::class);
        $verifyUrl = Event::dispatched(UserRegistered::class)[0][0]->url;

        $this->defaultTest(
            $this->method,
            $this->uri . '?' . parse_url($verifyUrl)['query'],
            needAuth: false,
        )->assertJsonStructure(['message']);

        $this->assertNotNull(User::where('email', 'test@test.com')->first()->email_verified_at);
    }

    public function testShouldResponseWithHttpForbiddenIfExpired()
    {
        Event::fake();
        Carbon::setTestNow(Carbon::yesterday());

        $this->defaultTest(
            Request::METHOD_POST,
            '/auth/registration',
            data: [
                'email' => 'test@test.com',
                'name' => 'Test',
                'password' => '11111111',
                'password_confirmation' => '11111111'
            ],
            needAuth: false,
        );

        Event::assertDispatched(UserRegistered::class);
        $verifyUrl = Event::dispatched(UserRegistered::class)[0][0]->url;

        Carbon::setTestNow();

        $this->defaultTest(
            $this->method,
            $this->uri . '?' . parse_url($verifyUrl)['query'],
            Response::HTTP_FORBIDDEN,
            needAuth: false,
        );

        $this->assertNull(User::where('email', 'test@test.com')->first()->email_verified_at);
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
            needAuth: false,
        );
    }
}
