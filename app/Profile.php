<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['user'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'presentation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        return $this->belongsTo(Media::class);
    }

    public function cover()
    {
        return $this->belongsTo(Media::class);
    }

    public function background()
    {
        return $this->belongsTo(Media::class);
    }

    public function portfolio()
    {
        return $this->belongsToMany(Media::class, 'media_profile', 'profile_id', 'media_id');
    }
}
