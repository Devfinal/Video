<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDatatypeTitleContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE posts MODIFY title  BLOB;');
        DB::statement('ALTER TABLE posts MODIFY content  BLOB;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE posts MODIFY title  VARCHAR(255);');
        DB::statement('ALTER TABLE posts MODIFY content LONGTEXT;');    }
}
