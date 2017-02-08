<?php

namespace App\Events;

use App\Media;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class ProfileImageUploaded
{
    use InteractsWithSockets, SerializesModels;

    /**
     * The uploaded file.
     *
     * @var UploadedFile
     */
    public $file;

    /**
     * The media object created by the upload.
     *
     * @var Media
     */
    public $image;

    /**
     * The associated user.
     *
     * @var User
     */
    public $user;

    /**
     * @param User $user
     * @param Media $image
     * @param UploadedFile $file
     */
    public function __construct(User $user, $image, UploadedFile $file)
    {
        $this->user = $user;
        $this->image = $image;
        $this->file = $file;
    }
}
