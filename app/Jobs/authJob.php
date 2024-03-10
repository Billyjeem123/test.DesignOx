<?php

namespace App\Jobs;

use App\Mail\WelcomeEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class authJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $fullname;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $fullname)
    {
        $this->email = $email;
        $this->fullname = $fullname;
    }

    /**
     * Execute the job.
     *x
     * @return void
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new WelcomeEmail($this->fullname));
    }
}
