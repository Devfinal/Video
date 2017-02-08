<?php

use App\Media;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/media/transcode/done', function (Request $request) {
    Log::debug('incomming transcode request...');

    switch ($request->header('x-amz-sns-message-type')) {
        case 'SubscriptionConfirmation':
            $client = new \GuzzleHttp\Client;
            $response = $client->request('GET', $request->json('SubscribeURL'));

            Log::debug('media.transcode.subscription-confirmation', ['status' => $response->getStatusCode()]);

            break;
        case 'Notification':
            Log::debug('media.transcode.done', ['content' => $request->json()]);

            $message = json_decode($request->json('Message'), true);
            $inputFile = $message['input']['key'];
            $outputFile = $message['outputs'][0]['key'];
            $transcodedFile = implode('', explode('-transcoded', $outputFile));
            $directory = pathinfo($transcodedFile, PATHINFO_DIRNAME);
            $thumbnailPattern = array_key_exists('thumbnailPattern', $message['outputs'][0]) ? $message['outputs'][0]['thumbnailPattern'] : null;
            $media = Media::where('path', $inputFile)->get();
            $thumbnail = null;
            $blurry = null;

            if ($media->isEmpty()) {
                Log::error('Could not find matching media', $message);

                return;
            }

            if (Storage::exists($transcodedFile)) {
                Storage::delete($transcodedFile);
            }

            // if (Storage::has($inputFile)) {
            //     Storage::delete($inputFile);
            // }

            Storage::move($outputFile, $transcodedFile);

            // Replace thumbnails in case some of them are broken
            if (!is_null($thumbnailPattern)) {
                $thumbnailPath = pathinfo($thumbnailPattern, PATHINFO_DIRNAME);
                $firstThumbnail = str_replace('{count}', '00001.png', $thumbnailPattern);

                if (Storage::exists($thumbnailPath)) {
                    if (Storage::exists($firstThumbnail)) {
                        $image = Image::make(Storage::get($firstThumbnail));
                        $image->resize(380, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });

                        $thumbnailHash = md5((string) $image->encode('jpg'));

                        Storage::put("{$directory}/{$thumbnailHash}.jpg", (string) $image->encode('jpg'));

                        $image->blur(32);
                        $image->blur(32);
                        $image->blur(32);
                        $image->blur(32);

                        $blurryHash = md5((string) $image->encode('jpg'));

                        Storage::put("{$directory}/{$blurryHash}.jpg", (string) $image->encode('jpg'));

                        $thumbnail = Media::create([
                            'type' => 'image',
                            'mime_type' => $image->mime(),
                            'name' => $media->first()->name . '-thumbnail',
                            'path' => "{$directory}/{$thumbnailHash}.jpg",
                            'width' => $image->width(),
                            'height' => $image->height()
                        ]);

                        $blurry = Media::create([
                            'type' => 'image',
                            'mime_type' => $image->mime(),
                            'name' => $media->first()->name . '-blurry',
                            'path' => "{$directory}/{$blurryHash}.jpg",
                            'width' => $image->width(),
                            'height' => $image->height()
                        ]);
                    }

                    Storage::deleteDirectory($thumbnailPath);
                }
            }

            $media->each(function (Media $media) use ($transcodedFile, $thumbnail, $blurry) {
                $media->path = $transcodedFile;

                if (!is_null($thumbnail)) {
                    $media->thumbnail_id = $thumbnail->id;
                }

                if (!is_null($blurry)) {
                    $media->blurry_id = $blurry->id;
                }

                $media->save();
            });

            break;
        case 'UnsubscribeConfirmation':
            // ...

            break;
    }

    return response('', 200);
});
