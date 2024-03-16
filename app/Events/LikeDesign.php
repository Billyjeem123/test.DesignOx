<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LikeDesign
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public mixed $email;
    public mixed $fullname;
    public  $designUrl;
    public mixed $project_title;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($email ,$fullname, $designUrl, $project_title)
    {
        $this->email = $email;
        $this->fullname  =$fullname;
        $this->designUrl = $designUrl;
        $this->project_title = $project_title;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
