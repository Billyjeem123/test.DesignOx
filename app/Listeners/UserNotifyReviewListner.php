<?php

namespace App\Listeners;

use App\Events\NotifyReview;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Notifications\DesignReview;


class UserNotifyReviewListner implements  ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NotifyReview $event): void
    {
        #  Dispatch the notification
        $user = User::where('email', $event->credential['author_email'])->first();

        if ($user) {
            $user->notify(new DesignReview($event->credential));
        }
    }

}
