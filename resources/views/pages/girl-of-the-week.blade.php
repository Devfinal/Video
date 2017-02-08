@extends('layouts.main')

@section('content')
    @if ($posts->count() > 0)
        <div class="row mb-4">
            <div class="col-lg-8 post-list post-list--botm">
                <div class="card post">
                    @if ($posts->first()->media->count() > 0)
                        <img src="{{ Storage::url($posts->first()->media->first()->getImage(!$posts->first()->media->first()->protected)->path) }}"
                             class="card-img-top img-fluid w-100">
                    @endif
                    <div class="card-block">
                        <h2 class="card-title text-uppercase">Babe of the Month</h2>
                        <h4 class="card-title mb-0">{{ $posts->first()->title }}</h4>
                        <p class="card-text mb-0">{{ \App\Country::getString($posts->first()->getMeta('country')) }}
                            : {{ $posts->first()->getMeta('place') }}</p>
                        <p class="card-text"><strong>{{ $posts->first()->getMeta('label') }}</strong></p>
                    </div>
                </div>
                <a href="{{ route('botm.show', $posts->first()) }}" class="link-wrapper"></a>
            </div>
            <div class="col-lg-4">
                <div class="card post-list post-list--small post-list--botm">
                    <div class="card-header bg-inverse text-light">
                        <strong>Latest articles</strong>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($posts->take(5) as $post)
                            <a class="list-group-item list-group-item-action"
                               href="{{ route('botm.show', $post) }}">
                                <div class="media">
                                    @if ($post->media->count() > 0)
                                        <div class="media-left">
                                            <img src="{{ Storage::url($post->media->first()->getThumbnail(!$post->media->first()->protected)->path) }}"
                                                 class="media-object"
                                                 width="120">
                                        </div>
                                    @endif
                                    <div class="media-body">
                                        <h4 class="media-heading">{{ $post->title }}</h4>
                                        <p class="mb-0">
                                            {{ \App\Country::getString($post->getMeta('country')) }}: {{ $post->getMeta('place') }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>{{ $post->getMeta('label') }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($posts->count() > 1)
        <div class="row post-list post-list--botm">
            @foreach ($posts->slice(1) as $post)
                <div class="col-lg-4">
                    <div class="card post">
                        @if ($post->media->count() > 0)
                            <img src="{{ Storage::url($post->media->first()->getThumbnail(!$post->media->first()->protected)->path) }}"
                                 class="card-img-top img-fluid">
                        @endif
                        <div class="card-block">
                            <h4 class="card-title mb-0">{{ $post->title }}</h4>
                            <p class="card-text mb-0">{{ \App\Country::getString($post->getMeta('country')) }}: {{ $post->getMeta('place') }}</p>
                            <p class="card-text"><strong>{{ $post->getMeta('label') }}</strong></p>
                        </div>
                    </div>
                    <a href="{{ route('botm.show', $post) }}" class="link-wrapper"></a>
                </div>
            @endforeach
        </div>
    @endif

    <nav class="text-xs-center">
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </nav>
@endsection
