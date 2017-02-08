<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['image', 'video']);
            $table->string('name');
            $table->boolean('protected')->default(false);
            $table->longText('caption')->nullable();
            $table->string('path');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('thumbnail_id')->unsigned()->nullable();
            $table->foreign('thumbnail_id')->references('id')->on('media');
            $table->integer('blurry_id')->unsigned()->nullable();
            $table->foreign('blurry_id')->references('id')->on('media');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
