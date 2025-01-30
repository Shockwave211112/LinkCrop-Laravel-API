<?php

namespace App\Modules\Auth\Listeners;

use App\Modules\Auth\Events\PasswordResetting;
use App\Modules\Auth\Mail\PasswordResetMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class PasswordResetListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordResetting $event): void
    {
        Mail::to($event->user->email)->send(new PasswordResetMail($event->user, $event->url));
    }
}
