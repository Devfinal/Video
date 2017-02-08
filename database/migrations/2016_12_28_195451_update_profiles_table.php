<?php

use App\Profile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Profile::get()->each(function (Profile $profile) {
            if (!is_null($profile->user)) {
                $settings = $profile->user->getMeta('settings', new Collection);

                $settings->put('first_name', $profile->first_name);
                $settings->put('last_name', $profile->last_name);
                $settings->put('phone_number', $profile->phone);
                $settings->put('ssn', $profile->ssn);

                $profile->user->setMeta('settings', $settings);
            }
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('phone');
            $table->dropColumn('ssn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('ssn');
        });

        Profile::get()->each(function (Profile $profile) {
            if (!is_null($profile->user)) {
                $settings = $profile->user->getMeta('settings', new Collection);

                $profile->first_name = $settings->get('first_name');
                $profile->last_name = $settings->get('last_name');
                $profile->phone = $settings->get('phone_number');
                $profile->ssn = $settings->get('ssn');

                $settings->forget([
                    'first_name',
                    'last_name',
                    'phone_number',
                    'ssn'
                ]);

                $profile->user->setMeta('settings', $settings);
                $profile->save();
            }
        });
    }
}
