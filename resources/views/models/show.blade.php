@extends('layouts.model')

@if (!is_null($user->profile->background))
    @push('styles')
    <style>
        @media screen and (min-width: 1200px) {
            body {
                background-image: url({{ Storage::url($user->profile->background->path) }});
            }
        }
    </style>
    @endpush
@endif

@section('model-content')
    <div class="mb-4">
        <h4 class="mb-4">{{ $user->username }}</h4>
        {!! $user->profile->presentation !!}
    </div>
    <div class="row grid-list post-list mb-4">
        @foreach ($posts as $post)
            <div class="col-sm-6 col-md-4 grid-item">
                @include('partials.posts.grid-item', ['post' => $post])
            </div>
        @endforeach
    </div>

    @if (!Auth::check() || !Auth::user()->hasActiveSubscription())
        <div class="card card-block text-xs-center p-4">
            <h4 class="card-title">Become a member</h4>
            <p class="card-text mb-4">To get full access to all articles and blogposts for ALL our girls you need an
                active subscription</p>
            <a href="{{ url('/register' . (isset($registerForModel) ? '?' . http_build_query(['model' => $registerForModel]) : '')) }}"
               class="btn btn-primary btn-lg btn-display mb-2">
                Get Full Access
            </a>
        </div>
    @endif

    <nav class="text-xs-center">
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </nav>
@endsection
