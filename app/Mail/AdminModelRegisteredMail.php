<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminModelRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The registered user.
     *
     * @var User
     */
    public $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Model registered: {$this->user->username}")
            ->view('emails.model-registered-mail');
    }
}
