<?php

namespace Tests\Feature\Links;

use App\Modules\Links\Jobs\LinkHit;
use App\Modules\Links\Models\Link;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReferralTest extends TestCase
{
    protected $method = Request::METHOD_GET;
    protected string $uri = '/l/';

    public function testShouldResponseWithHttpOk()
    {
        Queue::fake();

        $link = Link::factory()->create();

        $this->defaultTest(
            $this->method,
            $this->uri . $link->referral,
            Response::HTTP_FOUND,
            needAuth: false
        )->assertRedirect($link->origin);

        Queue::assertPushed(LinkHit::class);
    }

    public function testShouldResponseWithHttpNotFound()
    {
        Queue::fake();

        $this->defaultTest(
            $this->method,
            $this->uri . 'random_1234567',
            Response::HTTP_NOT_FOUND,
            needAuth: false
        );

        Queue::assertNotPushed(LinkHit::class);
    }
}
