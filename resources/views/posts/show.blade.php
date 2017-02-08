@extends('layouts.main')

@if (!is_null($post->user->profile->background))
    @push('styles')
    <style>
        @media screen and (min-width: 1200px) {
            body {
                background-image: url({{ Storage::url($post->user->profile->background->path) }});
            }
        }
    </style>
    @endpush
@endif

@section('content')
    @if (!is_null($post->user->profile->cover))
        <header class="card profile-header mb-4">
            <a href="{{ route('models.show', $post->user) }}">
                <img src="{{ Storage::url($post->user->profile->cover->path) }}"
                     class="card-img-top img-fluid w-100">
            </a>
        </header>
    @endif

    <div class="row">
        <div class="col-lg-9">
            <h1 class="post-title mb-0">
                {{ $post->title }}
                @if (Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->id === $post->user->id))
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-warning">edit</a>
                @endif
            </h1>
            <p class="text-muted mb-3">
                <i class="fa fa-clock-o"></i>
                {{ $post->updated_at }}
            </p>
            <div class="post-content mb-4 clearfix">{!! $post->content !!}</div>

            @if (count($post->media) > 0)
                <div class="gallery-wrapper mb-2">
                        <?php $imgNumber = 0; ?>
                        @foreach ($post->media as $media)
                        <div class="card post">

                            <div style="position:relative">
                                    <div style="position:relative">
                                        @if ($media->type === 'image')
                                            <img src="{{ Storage::url($media->path) }}"
                                                 class="card-img-top img-fluid w-100 @if ($imgNumber && !Auth::user()) img-blur @endif">
                                        @elseif ($media->type === 'video')
                                            <i class="fa fa-play-circle-o fa-5x play-icon"></i>
                                            <video class="video-js @if ($imgNumber && !Auth::user()) video-blur  @endif" preload="auto" data-setup='{"height": "auto"}' controls>
                                                <source src="{{ Storage::url($media->path) }}">
                                            </video>
                                        @endif

                                        @if ($imgNumber && !Auth::user())
                                            <div class="vip-sign text-light text-xs-center " >
                                                <div class="vip-sign--inner">
                                                    <span class="mb-1 h6 text-xs-center" style="display: block">Members Only</span>
                                                    <a href="{{ url('/register' . (isset($registerForModel) ? '?' . http_build_query(['model' => $registerForModel]) : '')) }}"
                                                       class="btn btn-primary  btn-display float-xs-right mb-1">
                                                        Get Full Access
                                                    </a>
                                                    <div class="clearfix"></div>
                                                    <small class="text-muted mb-0" >Local Scandinavian Girls</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @if ($imgNumber && !Auth::user())
                                    <a href="/register"
                                       class="link-wrapper"></a>
                                @endif
                            </div>

                        </div>

                        <div class="post-content mb-4 clearfix"></div>
                        <?php $imgNumber++; ?>
                        @endforeach
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
        <div class="col-lg-3">
            <div class="card post-list post-list--small post-list--botm">
                <div class="card-header bg-inverse text-light">
                    <strong>Latest images</strong>
                </div>
                <div class="card-block">
                    <div class="row grid-list">
                        @foreach ($relatedMedia as $media)
                            <div class="col-xs-4 grid-item p-0">
                                <a href="#" class="photoswipe-link">
                                    <img src="{{ Storage::url($media->getThumbnail(!($media->protected && (!Auth::check() || (!Auth::user()->hasActiveSubscription() && Auth::user()->id !== $post->user->id))))->path) }}"
                                         data-src="{{ Storage::url($media->getThumbnail(!($media->protected && (!Auth::check() || (!Auth::user()->hasActiveSubscription() && Auth::user()->id !== $post->user->id))))->path) }}"
                                         data-width="{{ $media->getThumbnail(!($media->protected && (!Auth::check() || (!Auth::user()->hasActiveSubscription() && Auth::user()->id !== $post->user->id))))->width }}"
                                         data-height="{{ $media->getThumbnail(!($media->protected && (!Auth::check() || (!Auth::user()->hasActiveSubscription() && Auth::user()->id !== $post->user->id))))->height }}"
                                         data-src-original="{{ Storage::url($media->getImage(!($media->protected && (!Auth::check() || (!Auth::user()->hasActiveSubscription() && Auth::user()->id !== $post->user->id))))->path) }}"
                                         data-width-original="{{ $media->getImage(!($media->protected && (!Auth::check() || (!Auth::user()->hasActiveSubscription() && Auth::user()->id !== $post->user->id))))->width }}"
                                         data-height-original="{{ $media->getImage(!($media->protected && (!Auth::check() || (!Auth::user()->hasActiveSubscription() && Auth::user()->id !== $post->user->id))))->height }}"
                                         class="img-fluid image">
                                    @if ($media->type === 'video')
                                        <i class="fa fa-play-circle-o fa-3x play-icon"></i>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
