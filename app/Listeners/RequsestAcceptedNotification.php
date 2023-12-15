<?php

namespace App\Listeners;

use App\Events\RequestAccept;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;

class RequsestAcceptedNotification
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
    public function handle(RequestAccept $event): void
    {
        $user = $event->user;

        $content = [
            'user'=>$user,
        ];

        Mail::to($user->email)->send(new RequestedAcceptedMail($content));
    }
}
