<?php

namespace App\Listeners;

use App\Events\PostMediaUploaded;
use App\Media;
use Image;
use Storage;

class CreateMediaThumbnails
{
    /**
     * Handle the event.
     *
     * @param PostMediaUploaded $event
     * @return void
     */
    public function handle(PostMediaUploaded $event)
    {
        if ($event->media->type === 'image') {
            $image = Image::make($event->file);
            $image->resize(380, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->save();

            $thumbnail = Media::create([
                'type' => 'image',
                'mime_type' => $event->file->getMimeType(),
                'name' => $event->media->name . '-thumbnail',
                'path' => Storage::putFile("media/{$event->post->user->id}", $event->file),
                'width' => $image->width(),
                'height' => $image->height()
            ]);
            $event->media->thumbnail_id = $thumbnail->id;

            $image->blur(32);
            $image->blur(32);
            $image->blur(32);
            $image->blur(32);
            $image->save();

            $blurry = Media::create([
                'type' => 'image',
                'mime_type' => $event->file->getMimeType(),
                'name' => $event->media->name . '-blurry',
                'path' => Storage::putFile("media/{$event->post->user->id}", $event->file),
                'width' => $image->width(),
                'height' => $image->height()
            ]);
            $event->media->blurry_id = $blurry->id;

            $event->media->save();
        } else if ($event->media->type === 'video') {
            $image = Image::make($event->request->input("media-video-thumbnail.{$event->index}"));
            $image->resize(380, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->save($event->file->getPathName());

            $thumbnail = Media::create([
                'type' => 'image',
                'mime_type' => $event->file->getMimeType(),
                'name' => $event->media->name . '-thumbnail',
                'path' => Storage::putFile("media/{$event->post->user->id}", $event->file),
                'width' => $image->width(),
                'height' => $image->height()
            ]);
            $event->media->thumbnail_id = $thumbnail->id;

            $image->blur(32);
            $image->blur(32);
            $image->blur(32);
            $image->blur(32);
            $image->save();

            $blurry = Media::create([
                'type' => 'image',
                'mime_type' => $event->file->getMimeType(),
                'name' => $event->media->name . '-blurry',
                'path' => Storage::putFile("media/{$event->post->user->id}", $event->file),
                'width' => $image->width(),
                'height' => $image->height()
            ]);
            $event->media->blurry_id = $blurry->id;

            $event->media->save();
        }
    }
}
