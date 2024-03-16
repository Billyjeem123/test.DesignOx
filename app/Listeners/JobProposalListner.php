<?php

namespace App\Listeners;

use App\Events\JobProposal;
use App\Mail\clientNotifyProposal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class JobProposalListner  implements ShouldQueue
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
    public function handle(JobProposal $event)
    {
        Mail::to($event->credential['client_email'])->send(new clientNotifyProposal($event->credential));
    }
}
