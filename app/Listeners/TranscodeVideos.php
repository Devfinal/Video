<?php

namespace App\Listeners;

use App\Events\PostMediaUploaded;
use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Log;

class TranscodeVideos
{
    protected $transcoderClient;

    public function __construct()
    {
        $this->transcoderClient = new ElasticTranscoderClient([
            'credentials' => [
                'key' => env('AWS_ELASTIC_TRANSCODER_KEY'),
                'secret' => env('AWS_ELASTIC_TRANSCODER_SECRET')
            ],
            'region' => env('AWS_ELASTIC_TRANSCODER_REGION'),
            'version' => '2012-09-25'
        ]);
    }

    /**
     * Handle the event.
     *
     * @param PostMediaUploaded $event
     * @return void
     */
    public function handle(PostMediaUploaded $event)
    {
        if ($event->media->type === 'video') {
            $mediaPath = pathinfo($event->media->path, PATHINFO_DIRNAME);
            $mediaFilename = pathinfo($event->media->path, PATHINFO_FILENAME);

            $result = $this->transcoderClient->createJob([
                'PipelineId' => env('AWS_ELASTIC_TRANSCODER_PIPELINE'),
                'Input' => [
                    'Key' => $event->media->path
                ],
                'Output' => [
                    'Key' => "{$mediaPath}/{$mediaFilename}-transcoded.mp4",
                    'ThumbnailPattern' => "{$mediaPath}/{$mediaFilename}-thumbnails/{count}",
                    'PresetId' => env('AWS_ELASTIC_TRANSCODER_PRESET')
                ]
            ]);

            if (
                !isset($result['@metadata']['statusCode']) ||
                $result['@metadata']['statusCode'] < 200 ||
                $result['@metadata']['statusCode'] > 299
            ) {
                Log::error('Error creating transcoder job', $result);
            }
        }
    }
}
