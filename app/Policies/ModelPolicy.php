<?php

namespace App\Policies;

use App\User;
use App\Model;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModelPolicy
{
    use HandlesAuthorization;

    /**
     * Intersects the policy for special grants.
     *
     * @param User $user The user trying the action.
     * @return bool|void
     */
    public function before(User $user)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User $user
     * @param \App\Model $model
     * @return mixed
     */
    public function view(User $user, Model $model)
    {
        return $user->hasActiveSubscription() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User $user
     * @param \App\Model $model
     * @return mixed
     */
    public function update(User $user, Model $model)
    {
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User $user
     * @param \App\Model $model
     * @return mixed
     */
    public function delete(User $user, Model $model)
    {
        return $user->hasRole('admin');
    }
}
