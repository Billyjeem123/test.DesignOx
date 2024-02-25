<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class forgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $fullName;
    public $token;
    public $appname;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullName, $token, $appname)
    {
        $this->fullName = $fullName;
        $this->token = $token;
        $this->$appname = $appname;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Forget Password',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'email.forgetPassword',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Forget Password')
            ->view('email.forgetPassword')
            ->with([
                'fullName' => $this->fullName,
                'token' => $this->token,
                'appname' => $this->appname,
            ]);

    }
}
