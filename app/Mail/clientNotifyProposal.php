<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class clientNotifyProposal extends Mailable
{
    use Queueable, SerializesModels;

    private array $credential;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $credential)
    {
        $this->credential = $credential;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Proposal Received for  ' . $this->credential['project_title']
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
            view: 'email.clientProposalNotify',
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

    public function build()
    {
        return $this->subject('New Proposal Received for [' . $this->credential['project_title'] . ']')
        ->view('email.clientProposalNotify')
            ->with('credential', $this->credential);
    }
}
