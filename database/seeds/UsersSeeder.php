<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $user = User::create([
                'username' => 'admin',
                'email' => 'admin@beautiesfromheaven.com',
                'password' => Hash::make('123456'),
                'verified' => true
            ]);
            $user->roles()->save(Role::where('name', 'admin')->first());
        });
    }
}
