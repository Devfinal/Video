<?php

namespace App\Providers;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // As models should have unique usernames but not customers we need a custom
        // rule to allow for that.
        Validator::extend('unique_model', function ($attribute, $value, $parameters, $validator) {
            return User::whereHas('roles', function (Builder $query) {
                $query->where('name', 'model');
            })->where('username', $value)->count() === 0;
        }, 'The username is already taken');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
