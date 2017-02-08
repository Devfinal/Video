@extends('layouts.main')

@section('content')
@if ($girlOfTheWeek->count() > 0 && $settings->get('botm_count', 5) > 0)
<div class="gallery gallery--botm mb-3">
    @foreach ($girlOfTheWeek->take($settings->get('botm_count', 5)) as $post)
    <div class="item media post">
        @if ($post->media->count() > 0)
        <div class="media-left">
            <img src="{{ Storage::url($post->media->first()->getImage(!$post->media->first()->protected)->path) }}" class="media-object img-fluid">
            @if ($post->hasVideo())
            <i class="fa fa-play-circle-o play-icon"></i>
            @endif
        </div>
        @endif
        <div class="media-body row align-middle text-light">
            <div class="col-xs-12 text-xs-center">
                <img src="/images/crown.png" class="crown">
                <h1 class="text-uppercase font-weight-bold">Babe of the Month</h1>
                <h3 class="media-heading text-uppercase font-weight-normal mb-0">
                    {{ $post->title }}
                </h3>
                <p class="text-muted text-uppercase mb-0">
                    Published {{ $post->created_at->format('Y-m-d') }}
                </p>
                <img src="/images/crown-bottom.png" class="crown-bottom">
            </div>
        </div>
        <a href="{{ route('botm.show', $post) }}" class="link-wrapper"></a>
    </div>
    @endforeach
</div>
@endif

<div class="row grid-list post-list">
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-item stamp stamp-left model-list hidden-sm-down">
        @foreach ($models->take($settings->get('model_list_count', 20)) as $model)
        <?php $backgroundImage = isset($model->profile->cover) ? 'style="background-image: url(' . Storage::url($model->profile->cover->path) . ')"' : '' ?>
        <div class="card card-inverse">
            <div class="card-header bg-inverse">
                {{ $loop->iteration }}. {{ $model->username }}
            </div>
            <div class="card-block text-xs-center text-white" {!! $backgroundImage !!}>
                <div class="card-title">
                    {{ $model->username }}
                </div>
            </div>

            <a class="link-wrapper" href="{{ route('models.show', $model) }}"></a>
        </div>
        @endforeach
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-item stamp stamp-right post-list post-list--small hidden-sm-down">
        <div class="card">
            <div class="card-header bg-inverse text-light">
                <strong>Most popular</strong>
            </div>
            <div class="list-group list-group-flush">
                @foreach ($popularPosts as $post)
                <a class="list-group-item list-group-item-action"
                   href="{{ route('posts.show', ['model' => $post->user, 'post' => $post]) }}">
                    <span class="likes float-xs-right text-muted">
                        {{ $post->getLikesCount() }}
                        <i class="fa fa-heart fa-fw text-primary"></i>
                    </span>
                    <div class="post">
                        <strong>{{ $post->user->username }}</strong> - {{ $post->title }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @foreach ($posts as $post)
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-item hidden-sm-down">
        @include('partials.posts.grid-item', ['post' => $post])
    </div>
    @endforeach
    <div class="row hidden-md-up">
        <div class="col-xs-6 ">
            @foreach ($posts as $post)
            @if($loop->iteration%2 ==0)
            <div class="n">
                @include('partials.posts.grid-item', ['post' => $post])
            </div>
            @endif
            @endforeach
        </div>
       <div class="col-xs-6 ">
            @foreach ($posts as $post)
            @if($loop->iteration%2 ==1)
            <div class="n">
                @include('partials.posts.grid-item', ['post' => $post])
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

<nav class="text-xs-center">
    {{ $posts->links('vendor.pagination.bootstrap-4') }}
</nav>
@endsection
