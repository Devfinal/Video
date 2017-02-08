<?php

namespace App\Events;

use App\Media;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class PostMediaUploaded
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
    public $media;

    /**
     * The post associated with the file.
     *
     * @var Post
     */
    public $post;

    /**
     * The upload request.
     *
     * @var Request
     */
    public $request;

    /**
     * The media index if multiple media files where uploaded.
     *
     * @var int
     */
    public $index;

    /**
     * @param Post $post
     * @param Media $media
     * @param UploadedFile $file
     * @param Request $request
     * @param int $index
     */
    public function __construct(Post $post, Media $media, UploadedFile $file, Request $request, $index)
    {
        $this->media = $media;
        $this->file = $file;
        $this->post = $post;
        $this->request = $request;
        $this->index = $index;
    }
}
