<?php

namespace App\Modules\Auth\Events;

use App\Modules\Users\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    public User $user;

    /**
     * @var string
     */
    public string $url;

    /**
     * @param $user
     * @param $url
     */
    public function __construct($user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }
}
