<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\AdminModelRegisteredMail;
use Mail;
use Swift_TransportException;

class NotifyAdminOfModelRegistration
{
    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        if ($event->user->hasRole('model')) {
            try {
                Mail::to('support@beautiesfromheaven.com')
                    ->send(new AdminModelRegisteredMail($event->user));
            } catch (Swift_TransportException $exception) {
                // we don't care if the email address is valid
            }
        }
    }
}
