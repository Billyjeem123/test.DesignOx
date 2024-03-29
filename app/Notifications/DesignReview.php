<?php

namespace App\Notifications;

use App\Mail\UserNotifyReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class DesignReview extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public array $reviewCredentials;
    public function __construct(array $reviewCredentials)
    {
        $this->reviewCredentials = $reviewCredentials;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notification: New Review on Design: ' . $this->reviewCredentials['project_title'])
            ->view('email.NotifyDesignReview', [
                'reviewCredentials' => $this->reviewCredentials,
                'fullName' => $this->reviewCredentials['author'],
                'reviewUrl' => $this->reviewCredentials['link_to_review'],
                'project_title' => $this->reviewCredentials['project_title']
            ]);
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'A new review has been left on your design.',
            'project_title' => $this->reviewCredentials['project_title'],
            'reviewUrl' => $this->reviewCredentials['link_to_review'],
        ];
    }

}
