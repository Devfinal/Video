<?php

namespace App\Listeners;

use App\Events\ProfileImageUploaded;
use App\Media;
use Image;
use Storage;

class ProfileImageUploadedListener
{
    /**
     * Handle the event.
     *
     * @param ProfileImageUploaded $event
     * @return void
     */
    public function handle(ProfileImageUploaded $event)
    {
        $image = Image::make($event->file);
        $image->resize(256, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save();

        $thumbnail = Media::create([
            'type' => 'image',
            'name' => $event->image->name . '-thumbnail',
            'path' => Storage::putFile("media/{$event->user->id}/profile-images", $event->file),
            'width' => $image->width(),
            'height' => $image->height()
        ]);
        $event->image->thumbnail_id = $thumbnail->id;
        $event->image->save();
    }
}
