<div class="card post">
    <div class="card-header bg-inverse text-light">

        <div id="like-wrapper-{{ $post->id }}">
            @if (!$post->doesUserLike(Auth::user()) || Auth::check() && Auth::user()->hasRole('admin'))
            <form id="like-post-{{ $post->id }}-form"
                  method="POST"
                  action="{{ route('posts.like', $post) }}"
                  class="ajax-like"
                  data-like-wrapper="#like-wrapper-{{ $post->id }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <a href="#"
                   class="float-xs-right text-muted no-decoration"
                   onclick="event.preventDefault();$('#like-post-{{ $post->id }}-form').trigger('submit')">
                    {{ $post->getLikesCount() }}
                    <i class="fa fa-heart fa-fw text-primary" style="position:relative">
                        <i class="fa fa-heart fa-fw heart-hollow"></i>
                    </i>
                </a>
            </form>
            @else
            <span class="float-xs-right text-muted">
                {{ $post->getLikesCount() }}
                <i class="fa fa-heart fa-fw text-primary"></i>
            </span>
            @endif
        </div>

        <i class="fa fa-clock-o"></i>
        {{ $post->updated_at->diffForHumans() }} by
        <strong><a href="{{ route('models.show', $post->user) }}"
                   class="text-light">{{ $post->user->username }}</a></strong>
    </div>
    <div style="position:relative">
        @if ($post->media->count() > 0)
        <div style="position:relative">
            <img src="{{ Storage::url($post->media->first()->getThumbnail(policy($post)->show(Auth::user(), $post))->path) }}"
                 class="card-img-top img-fluid w-100">
            @if ($post->hasVideo())
            <i class="fa fa-play-circle-o fa-5x play-icon"></i>
            @endif

            @if (!policy($post)->show(Auth::user(), $post))
            <div class="vip-sign text-light text-xs-center " >
                <div class="vip-sign--inner">
                    <span class="mb-1 h6 text-xs-center" style="display: inline">Members Only</span>
                    <a href="{{ url('/register' . (isset($registerForModel) ? '?' . http_build_query(['model' => $registerForModel]) : '')) }}"
                       class="btn btn-primary  btn-display float-xs-right mb-1">
                        Get Full Access
                    </a>
                    <div class="clearfix"></div>
                    <small class="text-muted mb-0" >Local Scandinavian Girls</small>
                </div>
                @if ($post->hasVideo())
                <i class="fa fa-play-circle-o fa-4x play-icon"></i>
                @endif
            </div>
            @endif
        </div>
        @endif
        <div class="card-block">
            @if($post->content)
            <p class="card-text">{{  substr(str_replace(['<p>','</p>'],'',$post->content), 0 ,85) }}</p>
            @else
            <p class="card-text">{{ $post->title }}</p>
            @endif

        </div>
        <a href="{{ route('posts.show', ['user' => $post->getUser(), 'post' => $post]) }}"
           class="link-wrapper"></a>
    </div>

</div>
