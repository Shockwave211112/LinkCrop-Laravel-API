<?php

namespace Tests\Feature\Auth;

use App\Modules\Users\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class OauthTest extends TestCase
{
    public function testShouldResponseWithHttpOk()
    {
        $socialiteUser = Mockery::mock('Laravel\Socialite\Two\User');
        $socialiteUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(Str::random(10))
            ->shouldReceive('getEmail')
            ->andReturn(Str::random(10) . '@example.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://google.com')
            ->shouldReceive('getToken')
            ->andReturn(Str::random(30));

        Socialite::shouldReceive('driver->stateless->user')->andReturn($socialiteUser);

        $this->get('/auth/google/callback')
            ->assertStatus(302)
            ->assertCookie('token');
    }

    public function testShouldResponseWithHttpOkIfAlreadyRegistered()
    {
        User::factory()->create(['email' => 'test@test.com']);
        $socialiteUser = Mockery::mock('Laravel\Socialite\Two\User');
        $socialiteUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(Str::random(10))
            ->shouldReceive('getEmail')
            ->andReturn('test@test.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://google.com')
            ->shouldReceive('getToken')
            ->andReturn(Str::random(30));

        Socialite::shouldReceive('driver->stateless->user')->andReturn($socialiteUser);

        $this->get('/auth/google/callback')
            ->assertStatus(302)
            ->assertCookie('token');
    }
}
