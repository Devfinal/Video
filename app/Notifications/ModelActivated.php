<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ModelActivated extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param User $user
     * @return MailMessage
     */
    public function toMail(User $user)
    {
        $loginUrl = route('login');

        return (new MailMessage)
            ->subject('Model confirmation')
            ->greeting('Congratulations!')
            ->line(<<<TEXT
We have accepted your application for beeing a model with us in Beautiesfromheaven.com
You now may log in to your account <a href="{$loginUrl}">here</a> to get started. There is a FAQ for you at your profile where you can find answers to things you might wonder about.
TEXT
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
