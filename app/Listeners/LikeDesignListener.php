<?php

namespace App\Listeners;

use App\Events\LikeDesign;
use App\Mail\LikeDesignMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class LikeDesignListener implements ShouldQueue
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
    public function handle(LikeDesign $event): void
    {
        Mail::to( $event->email)->send(new LikeDesignMailer($event->email, $event->fullname, $event->designUrl,$event->project_title));
    }
}
