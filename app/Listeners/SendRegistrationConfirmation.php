<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Notifications\CustomerRegistered;
use App\Notifications\ModelRegistered;
use Swift_TransportException;

class SendRegistrationConfirmation
{
    /**
     * Handle the event.
     *
     * @param UserRegistered $event
     */
    public function handle(UserRegistered $event)
    {
        if ($event->user->hasRole('customer')) {
            try {
                $event->user->notify(new CustomerRegistered);
            } catch (Swift_TransportException $exception) {
                // we don't care if the email address is valid
            }
        } else if ($event->user->hasRole('model')) {
            try {
                $event->user->notify(new ModelRegistered);
            } catch (Swift_TransportException $exception) {
                // we don't care if the email address is valid
            }
        }
    }
}
