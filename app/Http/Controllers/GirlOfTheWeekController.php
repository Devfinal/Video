<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use View;

class GirlOfTheWeekController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Post $post)
    {
        return View::make('gotw.show')
            ->with(compact('post'));
    }
}
