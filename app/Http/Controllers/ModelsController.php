<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use View;

class ModelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('models.index')
            ->with(
                'models',
                User::where([
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
            );
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->setMeta('visit_count', $user->getMeta('visit_count', 0) + 1);

        $posts = $user->posts()
            ->orderBy('updated_at', 'desc')
            ->paginate(32);
        $registerForModel = $user->id;

        return View::make('models.show')
            ->with(compact('user', 'posts', 'registerForModel'));
    }
}
