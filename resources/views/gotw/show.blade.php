@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-lg-8 post">
            @if (count($post->media) > 0)
                <div class="gallery-wrapper">
                    <div class="gallery">
                        @foreach ($post->media as $media)
                            <div class="item">
                                <a href="{{ ($media->protected && !policy($post)->show(Auth::user(), $post)) ? url('/register') : '#' }}">
                                    @if ($media->type === 'image')
                                        <figure class="lazyload"
                                                data-src="{{ Storage::url($media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }}"
                                                data-width="{{ $media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->width }}"
                                                data-height="{{ $media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->height }}"
                                                data-src-original="{{ Storage::url($media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }}"
                                                data-width-original="{{ $media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->width }}"
                                                data-height-original="{{ $media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->height }}">
                                            <div class="image"
                                                 style="background-image: url({{ Storage::url($media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }})"></div>
                                        </figure>
                                    @elseif ($media->type === 'video')
                                        @if (!($media->protected && !policy($post)->show(Auth::user(), $post)))
                                            <i class="fa fa-play-circle-o fa-5x play-icon"></i>
                                            <video class="video-js"
                                                   preload="auto"
                                                   data-setup='{"height": "auto"}'
                                                   controls>
                                                <source src="{{ Storage::url($media->path) }}">
                                            </video>
                                        @else
                                            <figure class="lazyload"
                                                    data-src="{{ Storage::url($media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }}"
                                                    data-width="{{ $media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->width }}"
                                                    data-height="{{ $media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->height }}"
                                                    data-src-original="{{ Storage::url($media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }}"
                                                    data-width-original="{{ $media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->width }}"
                                                    data-height-original="{{ $media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->height }}">
                                                <div class="image"
                                                     style="background-image: url({{ Storage::url($media->getImage(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }})">
                                                    @if (!($media->protected && !policy($post)->show(Auth::user(), $post)))
                                                        <i class="fa fa-play-circle-o fa-5x play-icon"></i>
                                                    @endif
                                                </div>
                                            </figure>
                                        @endif
                                    @endif
                                    @if (($media->protected && !policy($post)->show(Auth::user(), $post)))
                                        <div class="vip-sign text-light text-xs-center">
                                            <div class="vip-sign--inner px-4 py-2">
                                                <h4 class="mb-2">Members Only</h4>
                                                <button class="btn btn-primary btn-lg btn-display mb-1">
                                                    Get Full Access
                                                </button>
                                                <small class="text-muted d-block mb-0">Local Scandinavian Girls</small>
                                            </div>
                                            @if ($media->type === 'video')
                                                <i class="fa fa-play-circle-o fa-4x play-icon"></i>
                                            @endif
                                        </div>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if ($post->media->count() > 1)
                        <div class="gallery gallery--thumbnails">
                            @foreach ($post->media as $media)
                                <div class="item">
                                    <a href="#">
                                        <figure class="lazyload"
                                                data-src="{{ Storage::url($media->getThumbnail(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }}"
                                                data-width="{{ $media->width }}"
                                                data-height="{{ $media->height }}"
                                                data-src-original="{{ Storage::url($media->getThumbnail(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }}"
                                                data-width-original="{{ $media->width }}"
                                                data-height-original="{{ $media->height }}">
                                            <div class="image"
                                                 style="background-image: url({{ Storage::url($media->getThumbnail(!($media->protected && !policy($post)->show(Auth::user(), $post)))->path) }})">
                                                @if ($media->type === 'video')
                                                    <i class="fa fa-play-circle-o fa-2x play-icon"></i>
                                                @endif
                                            </div>
                                        </figure>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div id="like-wrapper-{{ $post->id }}">
                <form method="POST"
                      action="{{ route('posts.like', $post) }}"
                      class="form-inline mb-4 ajax-like"
                      data-like-wrapper="#like-wrapper-{{ $post->id }}">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}

                    @if (!$post->doesUserLike(Auth::user()) || Auth::check() && Auth::user()->hasRole('admin'))
                        <button type="submit" class="btn btn-link pl-0 pr-0 text-muted">
                            <i class="fa fa-fw fa-heart"></i>
                            Love and heart this
                        </button>
                        @if ($post->getLikesCount() > 0)
                            <span class="text-primary align-middle">({{ $post->getLikesCount() }}
                                loves and hearts)</span>
                        @endif
                    @else
                        <span class="text-primary align-middle">
                        <i class="fa fa-fw fa-heart"></i>
                        You
                            @if ($post->getLikesCount() > 1)
                                (and {{ $post->getLikesCount() - 1 }}
                                other{{ $post->getLikesCount() - 1 > 1 ? 's' : '' }})
                            @endif
                            loves and hearts this
                    </span>
                    @endif
                </form>
            </div>

            <h1 class="post-title mb-3">{{ $post->title }}</h1>
            <div class="post-content mb-4">{!! $post->content !!}</div>

            @if (!Auth::check() || !Auth::user()->hasActiveSubscription())
                <div class="card card-block text-xs-center p-4">
                    <h4 class="card-title">Become a member</h4>
                    <p class="card-text mb-4">To get full access to all articles and blogposts for ALL our girls you
                        need an active subscription</p>
                    <a href="{{ url('/register' . (isset($registerForModel) ? '?' . http_build_query(['model' => $registerForModel]) : '')) }}"
                       class="btn btn-primary btn-lg btn-display mb-2">
                        Get Full Access
                    </a>
                </div>
            @endif
        </div>
        <div class="col-lg-4 post-list">
            <div class="card post-list post-list--small post-list--botm">
                <div class="card-header bg-inverse text-light">
                    <strong>Latest articles</strong>
                </div>
                <div class="list-group list-group-flush">
                    @foreach ($post->user->posts()->whereNotIn('id', [$post->id])->orderBy('created_at', 'desc')->take(5)->get() as $post)
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
                                        {{ \App\Country::getString($post->getMeta('country')) }}
                                        : {{ $post->getMeta('place') }}
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
@endsection
