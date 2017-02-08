<?php

namespace App\Policies;

use App\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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
     * Returns true if the user can view the given post.
     *
     * @param User|null $user The user wanting to read the post.
     * @param Post $post The post the user wishes to read.
     * @return bool
     */
    public function show($user, Post $post)
    {
        if (!$post->hasProtectedMedia()) {
            return true;
        }

        if ($user instanceof User) {
            // @note We check for admin role here so we can skip the before hook to allow guest access
            return $user->hasRole('admin') ||
                $user->hasActiveSubscription() ||
                $post->getUser()->id === $user->id;
        }

        return false;
    }

    /**
     * Returns true if the user can create the given post.
     *
     * @param User $user The user wanting to create the post.
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasRole('model');
    }

    /**
     * Returns true if the user can create the given post.
     *
     * @param User $user The user wanting to create the post.
     * @return bool
     */
    public function store(User $user)
    {
        return $user->hasRole('model');
    }

    /**
     * Returns true if the user can create the given post.
     *
     * @param User $user The user wanting to create the post.
     * @param Post $post The post the user wishes to edit.
     * @return bool
     */
    public function edit(User $user, Post $post)
    {
        return $post->user->id === $user->id;
    }

    /**
     * Returns true if the user can update the given post.
     *
     * @param User $user The user wanting to update the post.
     * @param Post $post The post the user wishes to update.
     * @return bool
     */
    public function update(User $user, Post $post)
    {
        return $post->user->id === $user->id && $post->created_at->gt(Carbon::parse('-5 days'));
    }

    /**
     * Returns true if the user can delete the given post.
     *
     * @param User $user The user wanting to delete the post.
     * @param Post $post The post the user wishes to delete.
     * @return bool
     */
    public function delete(User $user, Post $post)
    {
        return $post->user->id === $user->id && $post->created_at->gt(Carbon::parse('-5 days'));
    }
}
