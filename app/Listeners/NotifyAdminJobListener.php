<?php

namespace App\Listeners;

use App\Events\NotifyAdminJob;
use App\Mail\AdminJobNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminJobListener  implements  ShouldQueue
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
    public function handle(NotifyAdminJob $event): void
    {
        Mail::to($event->appmail)->send(new AdminJobNotify());
    }
}
