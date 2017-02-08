<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Storage;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'verification_token',
        'verified',
        'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'verification_token',
        'remember_token'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    public static function boot()
    {
        parent::boot();

        User::deleted(function (User $user) {
            // cleanup profile media
            if (!is_null($user->profile)) {
                Storage::delete($user->profile->portfolio->map(function (Media $media) {
                    return $media->path;
                })->all());

                $user->profile->portfolio()->delete();
            }

            // cleanup associated posts
            $user->posts->each(function (Post $post) {
                $post->delete();
            });
        });
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function meta()
    {
        return $this->belongsToMany(Meta::class);
    }

    /**
     * Returns a single meta value associated with this user.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getMeta($key, $default = null)
    {
        return $this->meta->contains('key', $key) ? $this->meta->where('key', $key)->first()->value : $default;
    }

    /**
     * Sets a single meta value associated with this user.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setMeta($key, $value)
    {
        if ($this->meta->contains('key', $key)) {
            $meta = $this->meta->where('key', $key)->first();
            $meta->value = $value;
            $meta->save();
        } else {
            $this->meta()->create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }

    /**
     * Deletes a single meta value associated with this user.
     *
     * @param string $key
     * @return void
     */
    public function deleteMeta($key)
    {
        $meta = $this->meta->where('key', $key)->first();

        if (!is_null($meta)) {
            $this->meta()->detach($meta->id);
        }
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Returns the total amount of likes the user has.
     *
     * @return int
     */
    public function getLikes()
    {
        return $this->posts()->get()->sum(function (Post $post) {
            return $post->getLikesCount();
        });
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'like_post');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Returns true if the user has the given role.
     *
     * @param string $role The role to check for.
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    /**
     * Returns true if the user has an active subscription.
     *
     * @return bool
     */
    public function hasActiveSubscription()
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->getMeta('active_subscription', false);
    }

    /**
     * Returns true if the user is currently on a grace period.
     *
     * @return bool
     */
    public function hasGracePeriod()
    {
        return $this->getMeta('subscription_canceled') && $this->getMeta('active_subscription');
    }

    /**
     * Returns the current balance of the given currency.
     *
     * @param string $currency
     * @return float
     */
    public function getBalance($currency = 'hearts')
    {
        return $this->transactions->where('currency', $currency)->sum('amount');
    }
}
