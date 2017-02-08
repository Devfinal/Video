<?php

namespace App\Events;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserRegistered
{
    use InteractsWithSockets, SerializesModels;

    /**
     * The registered user.
     *
     * @var User
     */
    public $user;

    /**
     * The request triggering the registration.
     *
     * @var Request
     */
    public $request;

    /**
     * @param User $user
     * @param Request $request
     */
    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }
}
