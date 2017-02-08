<?php

namespace App\Listeners;

use App\Notifications\ModelActivated;
use App\Events\ModelActivated as ModelActivatedEvent;
use Swift_TransportException;

class SendActivationConfirmation
{
    /**
     * Handle the event.
     *
     * @param  ModelActivatedEvent  $event
     * @return void
     */
    public function handle(ModelActivatedEvent $event)
    {
        try {
            $event->user->notify(new ModelActivated);
        } catch (Swift_TransportException $exception) {
            // we don't care if the email address is valid
        }
    }
}
