<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        AuthenticatesUsers::credentials as defaultCredentials;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    protected function credentials(Request $request)
    {
        $credentials = $this->defaultCredentials($request);
        $credentials['verified'] = 1;
        $credentials['active'] = 1;

        return $credentials;
    }

    protected function authenticated(Request $request, User $user)
    {
        if ($user->hasRole('admin')) {
            return redirect()
                ->route('admin');
        }

        if ($user->hasRole('model')) {
            return redirect()
                ->route('admin.posts.create');
        }
    }
}
