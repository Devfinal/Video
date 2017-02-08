<?php

namespace App\Http\Controllers\Auth;

use App\Country;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use GuzzleHttp\Client;
use Hash;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new user instance after a valid registration.
     *
     * @param Request $request
     * @return User
     */
    protected function create(Request $request)
    {
        $oldUser = User::withTrashed()->where('email', $request->input('email'))->first();

        // @note We stash away the old user to keep the statistics
        if (!is_null($oldUser)) {
            $oldUser->update([
                'email' => "deleted_user_{$oldUser->id}@example.com"
            ]);
        }

        $user = new User([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'username' => $request->input('username'),
            'verified' => true,
            'active' => true
        ]);

        $countryCode = '';
        $client = new Client;
        $response = $client->request('GET', "http://ip-api.com/json/{$request->ip()}");

        if ($response->getStatusCode() === 200) {
            $body = json_decode((string) $response->getBody(), true);

            if ($body['status'] === 'success') {
                $countryCode = Country::getValueByString($body['country']);
            }
        }

        DB::transaction(function () use ($user, $oldUser, $request, $countryCode) {
            $user->save();
            $user->roles()->save(Role::where('name', 'customer')->first());
            $user->setMeta('subscription', $request->input('subscription'));
            $user->setMeta('pending_payment', true);
            $user->setMeta('ip_address', $request->ip());
            $user->setMeta('country', $countryCode);

            if ($request->has('phone_number')) {
                $user->setMeta('phone_number', $request->input('phone_number'));
            }

            if (!is_null($oldUser)) {
                $user->setMeta('returning_customer', true);
            }
        });

        return $user;
    }

    /**
     * Verifies the verification token.
     *
     * @param string $token
     * @return User
     */
    protected function verifyToken($token)
    {
        $user = User::where('verification_token', $token)
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', 'customer');
            })
            ->firstOrFail();

        $user->verified = true;
        $user->verification_token = null;
        $user->save();

        return $user;
    }
}
