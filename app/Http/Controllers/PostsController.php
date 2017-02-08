<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Auth;
use Illuminate\Http\Request;
use URL;
use View;

class PostsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param User $user
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user, Post $post)
    {
        $user->setMeta('visit_count', $user->getMeta('visit_count', 0) + 1);

        if (!policy($post)->show(Auth::user(), $post)) {
            $model = strpos(URL::previous(), 'models') !== false ? '?model=' . $user->id : '';

            return Auth::check() ?
                redirect()
                    ->route('settings.edit')
                    ->with('message', 'Please upgrade your subscription') :
                redirect("/register{$model}");
        }

        return View::make('posts.show')
            ->with([
                'post' => $post,
                'registerForModel' => $user->id,
                'relatedMedia' => $user->posts()
                    ->whereNotIn('id', [$post->id])
                    ->get()
                    ->pluck('media')
                    ->flatten()
                    ->sortByDesc('updated_at')
                    ->take(30)
            ]);
    }
}
