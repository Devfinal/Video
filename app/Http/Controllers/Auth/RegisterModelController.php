<?php

namespace App\Http\Controllers\Auth;

use App\Country;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Media;
use App\Role;
use App\User;
use Auth;
use DB;
use Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Storage;
use View;

class RegisterModelController extends Controller
{
    use RedirectsUsers;

    /**
     * Where to redirect users after registration
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Displays the registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return View::make('auth.register-model');
    }

    /**
     * Handles a registration request for the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'username' => 'required|max:255|unique_model',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'country' => 'required|in:' . implode(',', Country::enum()),
            'phone' => 'required|max:255',
            'ssn' => 'required|max:255',
            'presentation' => 'required',
            'media' => 'required',
            'media.*.file' => 'mimetypes:image/gif,image/png,image/jpeg,image/bmp,video/mp4,video/x-m4v,video/webm,video/ogg,video/avi,video/mpeg,video/quicktime|max:' . 1024 * 1000,
            'terms' => 'required'
        ]);

        event(new UserRegistered($user = $this->create($request->all()), $request));

        if ($request->hasFile('media.*.file')) {
            $user->profile->portfolio()->saveMany(collect($request->file('media.*.file'))->map(function (
                UploadedFile $file
            ) use ($user) {
                $type = strstr($file->getMimeType(), '/', true);
                $width = null;
                $height = null;

                if ($type === 'image') {
                    list($width, $height) = getimagesize($file);
                }

                return new Media([
                    'type' => $type,
                    'mime_type' => $file->getMimeType(),
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'path' => Storage::putFile("media/{$user->id}/portfolio", $file),
                    'width' => $width,
                    'height' => $height
                ]);
            })->all());
        }

        return $this->registered($request, $user) ?: redirect($this->redirectPath())
            ->with('message', 'Your application is being processed');
    }

    /**
     * Verifies a verification token.
     *
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request, $token)
    {
        $this->verifyToken($token);

        return redirect()
            ->route('home')
            ->with('message', 'Your application is being processed');
    }

    /**
     * Creates a new user.
     *
     * @param array $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'username' => $data['username'],
            'verified' => true,
            'active' => false
        ]);

        DB::transaction(function () use ($user, $data) {
            $user->save();
            $user->profile()->create([
                'presentation' => $data['presentation']
            ]);

            $settings = $user->getMeta('settings', new Collection);
            $settings->put('first_name', $data['first_name']);
            $settings->put('last_name', $data['last_name']);
            $settings->put('country', $data['country']);
            $settings->put('phone_number', $data['phone']);
            $settings->put('ssn', $data['ssn']);
            $user->setMeta('settings', $settings);

            $user->roles()->save(Role::where('name', 'model')->first());
        });

        return $user;
    }

    /**
     * Verifies the given verification token.
     *
     * @param string $token
     * @return User
     */
    protected function verifyToken($token)
    {
        $user = User::where('verification_token', $token)->whereHas('roles', function (Builder $query) {
            $query->where('name', 'model');
        })->firstOrFail();

        $user->verified = true;
        $user->verification_token = null;
        $user->save();

        return $user;
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // ...
    }
}
