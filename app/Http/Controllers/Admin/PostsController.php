<?php

namespace App\Http\Controllers\Admin;

use App\Events\PostMediaUploaded;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Media;
use App\Post;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\UnauthorizedException;
use Image;
use Storage;
use View;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('admin.posts.index')
            ->with(
                'posts',
                Auth::user()->posts()
                    ->orderBy('updated_at', 'desc')
                    ->paginate(25)
            );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('create', Post::class)) {
            throw new UnauthorizedException;
        }

        return View::make('admin.posts.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePostRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        if ($request->user()->cannot('store', Post::class)) {
            throw new UnauthorizedException;
        }

        try {
            $post = $request->user()->posts()->create(array_merge($request->all(), ['type' => 'post']));
        } catch (QueryException $exception) {
            // @note Catch error when storing certain unsupported emojis
            if (preg_match("/General error: 1366.*for column '(.*?)'/", $exception->getMessage(), $matches) !== false) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        $matches[1] => 'The field cannot contain any invalid characters (like emojis)'
                    ]);
            }

            throw $exception;
        }

        if ($request->hasFile('media.*.file')) {
            $post->media()->saveMany(collect($request->file('media.*.file'))->map(function (
                UploadedFile $file,
                $index
            ) use ($post, $request) {
                $type = strstr($file->getMimeType(), '/', true);
                $width = null;
                $height = null;

                if ($type === 'image') {
                    list($width, $height) = getimagesize($file);

                    $image = Image::make($file);
                    $watermark = Image::make('images/watermark.png');
                    $watermark->resize(max(100, min(300, $image->width() / 4)), null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->insert($watermark, 'bottom-right', 10, 10);
                    $image->save();
                }

                $media = new Media([
                    'type' => $type,
                    'mime_type' => $file->getMimeType(),
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'path' => Storage::putFile("media/{$post->user->id}", $file),
                    'width' => $width,
                    'height' => $height,
                    'protected' => $request->input("media.{$index}.protected", 'off') === 'on'
                ]);

                event(new PostMediaUploaded($post, $media, $file, $request, $index));

                return $media;
            })->all());
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Successfully created post');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if (Auth::user()->cannot('edit', $post)) {
            throw new UnauthorizedException;
        }

        return View::make('admin.posts.edit')
            ->with(compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StorePostRequest $request
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(StorePostRequest $request, Post $post)
    {
        if ($request->user()->cannot('update', $post)) {
            throw new UnauthorizedException;
        }

        try {
            $post->update($request->all());
        } catch (QueryException $exception) {
            // @note Catch error when storing certain unsupported emojis
            if (preg_match("/General error: 1366.*for column '(.*?)'/", $exception->getMessage(), $matches) !== false) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        $matches[1] => 'The field cannot contain any invalid characters (like emojis)'
                    ]);
            }

            throw $exception;
        }

        $post->media->each(function (Media $media) use ($request) {
            $media->update([
                'protected' => $request->input("media.{$media->id}.protected", 'off') === 'on'
            ]);
        });

        if ($request->hasFile('media.*.file')) {
            $post->media()->saveMany(collect($request->file('media.*.file'))->map(function (
                UploadedFile $file,
                $index
            ) use ($post, $request) {
                $type = strstr($file->getMimeType(), '/', true);
                $width = null;
                $height = null;

                if ($type === 'image') {
                    list($width, $height) = getimagesize($file);

                    $image = Image::make($file);
                    $watermark = Image::make('images/watermark.png');
                    $watermark->resize(max(100, min(300, $image->width() / 4)), null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->insert($watermark, 'bottom-right', 10, 10);
                    $image->save();
                }

                $media = new Media([
                    'type' => $type,
                    'mime_type' => $file->getMimeType(),
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'path' => Storage::putFile("media/{$post->user->id}", $file),
                    'width' => $width,
                    'height' => $height,
                    'protected' => $request->input("media.{$index}.protected", 'off') === 'on'
                ]);

                event(new PostMediaUploaded($post, $media, $file, $request, $index));

                return $media;
            })->all());
        }

        if ($request->user()->hasRole('admin')) {
            return redirect()
                ->route('posts.show', [
                    'user' => $post->user,
                    'post' => $post
                ])
                ->with('success', 'Successfully updated post');
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Successfully updated post');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Post $post)
    {
        $post->delete();

        if ($request->user()->hasRole('admin')) {
            return redirect()
                ->route('models.show', [
                    'user' => $post->user
                ])
                ->with('success', 'Successfully removed post');
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Successfully removed post');
    }
}
