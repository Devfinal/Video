<?php

namespace App\Http\Controllers;

use App\Mail\SupportMail;
use App\Meta;
use App\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mail;
use Swift_TransportException;
use View;

class PagesController extends Controller
{
    /**
     * Display the start page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $siteMeta = Meta::where('key', 'site_meta')->first();
        $settings = !is_null($siteMeta) ? $siteMeta->value : new Collection;

        return View::make('pages/index')
            ->with([
                'settings' => $settings,

                'models' => User::where([
                    'verified' => true,
                    'active' => true
                ])
                    ->whereHas('roles', function (Builder $query) {
                        $query->where('name', 'model');
                    })
                    ->get()
                    ->sortByDesc(function (User $user) {
                        return $user->getMeta('visit_count', 0);
                    })
                    ->take(20),

                'popularPosts' => Post::where('type', 'post')
                    ->whereHas('user', function (Builder $query) {
                        $query->where('active', true);
                    })
                    ->whereBetween('updated_at', [Carbon::now()->subWeeks(2), Carbon::now()])
                    ->popular()
                    ->orderBy('likes_count', 'desc')
                    ->get()
                    ->filter(function (Post $post) {
                        return $post->getLikesCount() > 0;
                    })
                    ->unique('user')
                    ->take(10),

                'posts' => Post::where('type', 'post')
                    ->whereHas('user', function (Builder $query) {
                        $query->where('active', true);
                    })
                    ->orderBy('updated_at', 'desc')
                    ->paginate(32),

                'girlOfTheWeek' => Post::where('type', 'gotw')
                    ->latest()
                    ->take(5)
                    ->get()
            ]);
    }

    /**
     * Display the Girl of the Week page.
     *
     * @return \Illuminate\Http\Response
     */
    public function girlOfTheWeek()
    {
        $posts = Post::where('type', 'gotw')
            ->orderBy('created_at', 'desc')
            ->paginate(32);

        $posts->map(function (Post $post) {
            foreach ($post->media as $media) {
                if ($media->type === 'image') {
                    $post->image = $media;

                    break;
                }
            }

            return $post;
        });

        return View::make('pages/girl-of-the-week')
            ->with(compact('posts'));
    }

    /**
     * Display the terms page.
     *
     * @return \Illuminate\Http\Response
     */
    public function terms()
    {
        return View::make('pages/terms');
    }

    /**
     * Display the model terms page.
     *
     * @return \Illuminate\Http\Response
     */
    public function modelTerms()
    {
        return View::make('pages/model-terms');
    }

    /**
     * Display the privacy policy page.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacyPolicy()
    {
        return View::make('pages/privacy-policy');
    }

    /**
     * Display the content policy page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contentPolicy()
    {
        return View::make('pages/content-policy');
    }

    /**
     * Display the cookies information page.
     *
     * @return \Illuminate\Http\Response
     */
    public function cookies()
    {
        return View::make('pages/cookies');
    }

    /**
     * Display the contact page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        return View::make('pages/contact');
    }

    /**
     * Handle contact form submit.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function contactSubmit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|confirmed',
            'subject' => 'required',
            'description' => 'required'
        ]);

        try {
            Mail::to('support@beautiesfromheaven.com')
                ->send(new SupportMail($request));
        } catch (Swift_TransportException $exception) {
            // we don't care if the email address is valid
        }

        return redirect()
            ->back()
            ->with('message', 'Your support message have been sent');
    }
}
