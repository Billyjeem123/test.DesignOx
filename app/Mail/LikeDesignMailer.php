<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LikeDesignMailer extends Mailable
{
    use Queueable, SerializesModels;

    private mixed $fullname;
    private mixed $email;
    private mixed $designUrl;
    private mixed $project_title;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $email, $fullname,$designUrl, $project_title)
    {
        $this->fullname = $fullname;
        $this->email = $email;
        $this->designUrl = $designUrl;
        $this->project_title = $project_title;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notification: Your Design Received a Like',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.LikeDesign',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
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
        return $this->subject('Notification: Your Design Received a Like')
            ->view('email.LikeDesign')
            ->with([
                'fullName' => $this->fullname,
                'designUrl' => $this->designUrl,
                'project_title' => $this->project_title
            ]);

    }
}
