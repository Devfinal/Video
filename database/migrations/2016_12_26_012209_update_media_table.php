<?php

use App\Media;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('mime_type');
        });

        Media::where('type', 'image')->update([
            'mime_type' => 'image/jpg'
        ]);
        Media::where('type', 'video')->update([
            'mime_type' => 'video/mp4'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('mime_type');
        });
    }
}
