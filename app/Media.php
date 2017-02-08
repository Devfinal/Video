<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Image;

class Media extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'mime_type',
        'name',
        'protected',
        'caption',
        'path',
        'width',
        'height'
    ];

    /**
     * Returns the media image.
     *
     * Will return a blurry version of the image if $hasPermission is false.
     *
     * @param bool $hasPermission
     * @return Media
     */
    public function getImage($hasPermission = true)
    {
        return $hasPermission ?
            ($this->type === 'image' ? $this : $this->getThumbnail($hasPermission)) :
            $this->getThumbnail($hasPermission);
    }

    /**
     * Returns the media thumbnail.
     *
     * Will return a blurry version of the thumbnail if $hasPermission is false.
     *
     * @param bool $hasPermission
     * @return Media
     */
    public function getThumbnail($hasPermission = true)
    {
        return $hasPermission ?
            (!is_null($this->thumbnail) ? $this->thumbnail : $this) :
            (!is_null($this->blurry) ?
                $this->blurry :
                (!is_null($this->thumbnail) ? $this->thumbnail : $this));
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    public function blurry()
    {
        return $this->belongsTo(Media::class, 'blurry_id');
    }

    public static function boot()
    {
        parent::boot();

        // clean up the associated thumbnail/blurry versions
        Media::deleted(function (Media $media) {
            if (!is_null($media->thumbnail)) {
                $media->thumbnail->delete();
            }

            if (!is_null($media->blurry)) {
                $media->blurry->delete();
            }
        });
    }
}
