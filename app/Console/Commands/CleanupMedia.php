<?php

namespace App\Console\Commands;

use App\Media;
use Illuminate\Console\Command;
use Storage;

class CleanupMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes any dangling media files';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Storage::allFiles('media') as $file) {
            $media = Media::where('path', $file)->get();

            if ($media->isEmpty()) {
               Storage::delete($file);
            }
        }
    }
}
