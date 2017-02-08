@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            {{ isset($post) ? 'Edit' : 'New' }} post
        </div>
        <div class="card-block">
            <form id="save-post"
                  method="POST"
                  action="{{ isset($post) ? route('admin.botm.update', $post) : route('admin.botm.store') }}"
                  enctype="multipart/form-data"
                  novalidate>
                {{ csrf_field() }}

                @if(isset($post))
                    {{ method_field('PUT') }}
                @endif

                <fieldset {{ isset($post) && Auth::user()->cannot('update', $post) ? 'disabled' : '' }}>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('title') ? ' has-danger' : '' }}">
                                <label for="title" class="form-control-label">Title</label>
                                <input type="text"
                                       id="title"
                                       class="form-control{{ $errors->has('title') ? ' form-control-danger' : '' }}"
                                       name="title"
                                       value="{{ isset($post) ? $post->title : old('title') }}"
                                       required
                                       autocomplete="off">

                                @if ($errors->has('title'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('title') }}
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">

                                    <div class="form-group{{ $errors->has('model') ? ' has-danger' : '' }}">
                                        <label for="model" class="form-control-label d-block">Model</label>
                                        <select id="model" name="model" class="custom-select">
                                            <option value="">Freelance</option>

                                            @foreach ($models as $model)
                                                <option value="{{ $model->id }}" {{ isset($post) && $post->getMeta('user') == $model->id || old('model') == $model->id ? 'selected' : '' }}>{{ $model->username }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('model'))
                                            <div class="form-control-feedback">
                                                {{ $errors->first('model') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="form-group{{ $errors->has('country') ? ' has-danger' : '' }}">
                                        <label for="country" class="form-control-label d-block">Country</label>
                                        <select id="country" name="country" class="custom-select w-100">
                                            <optgroup>
                                                @foreach (collect(\App\Country::enum())->slice(0, 4) as $code)
                                                    <option value="{{ $code }}"{{ isset($post) && $post->getMeta('country') == $code || old('country') == $code ? ' selected' : '' }}>{{ \App\Country::getString($code) }}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup>
                                                @foreach (collect(\App\Country::enum())->slice(4) as $code)
                                                    <option value="{{ $code }}"{{ isset($post) && $post->getMeta('country') == $code || old('country') == $code ? ' selected' : '' }}>{{ \App\Country::getString($code) }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>

                                        @if ($errors->has('country'))
                                            <div class="form-control-feedback">
                                                {{ $errors->first('country') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group{{ $errors->has('label') ? ' has-danger' : '' }}">
                                        <label for="label" class="form-control-label">Label</label>
                                        <input type="text"
                                               id="label"
                                               class="form-control{{ $errors->has('label') ? ' form-control-danger' : '' }}"
                                               name="label"
                                               value="{{ isset($post) ? $post->getMeta('label') : old('label') }}"
                                               required
                                               autocomplete="off">

                                        @if ($errors->has('label'))
                                            <div class="form-control-feedback">
                                                {{ $errors->first('label') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="form-group{{ $errors->has('place') ? ' has-danger' : '' }}">
                                        <label for="place" class="form-control-label">Place</label>
                                        <input type="text"
                                               id="place"
                                               class="form-control{{ $errors->has('place') ? ' form-control-danger' : '' }}"
                                               name="place"
                                               value="{{ isset($post) ? $post->getMeta('place') : old('place') }}"
                                               required
                                               autocomplete="off">

                                        @if ($errors->has('place'))
                                            <div class="form-control-feedback">
                                                {{ $errors->first('place') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('media') || $errors->has('media.0.file') ? ' has-danger' : '' }}">
                                <label class="form-control-label">Picture/Video</label>
                                <input type="file"
                                       id="media"
                                       name="media[][file]"
                                       class="form-control media-upload"
                                       multiple
                                       accept="image/*,video/mp4,video/x-m4v,video/*">

                                @if ($errors->has('media'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('media') }}
                                    </div>
                                @endif

                                @if ($errors->has('media.0.file'))
                                    <div class="form-control-feedback">
                                        The files must be images or videos and cannot be more than 1G in size.
                                    </div>
                                @endif
                            </div>

                            @if (isset($post))
                                <div class="row sortable" data-post-id="{{ $post->id }}">
                                    @foreach ($post->media as $media)
                                        <div class="col-xs-6 col-lg-3 mb-2 draggable" data-media-id="{{ $media->id }}">
                                            <div class="card">
                                                <div style="position:relative">
                                                    <img src="{{ Storage::url($media->getThumbnail()->path) }}"
                                                         class="card-img-top img-fluid">
                                                    @if ($media->type === 'video')
                                                        <i class="fa fa-play-circle-o fa-4x play-icon"></i>
                                                    @endif
                                                </div>
                                                <div class="card-block">
                                                    <div class="checkbox float-xs-left">
                                                        <label class="custom-control custom-checkbox mb-0">
                                                            <input type="checkbox"
                                                                   name="media[{{ $media->id }}][protected]"
                                                                   class="custom-control-input"
                                                                    {{ $media->protected ? 'checked' : '' }}>
                                                            <span class="custom-control-indicator"></span>
                                                            <span class="custom-control-description">VIP</span>
                                                        </label>
                                                    </div>
                                                    <button type="button"
                                                            class="close delete-media-button"
                                                            data-post-id="{{ $post->id }}"
                                                            data-media-id="{{ $media->id }}">
                                                        <i class="fa fa-times text-danger"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="row media-previews pt-1 m-1 rounded"></div>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('content') ? ' has-danger' : '' }}">
                        <label for="content" class="form-control-label">Content</label>
                        <textarea id="content"
                                  class="form-control{{ $errors->has('content') ? ' form-control-danger' : '' }} tinymce"
                                  name="content">{{ isset($post) ? $post->content : old('content') }}</textarea>

                        @if ($errors->has('content'))
                            <span class="form-control-feedback">
                            {{ $errors->first('content') }}
                        </span>
                        @endif
                    </div>

                    @if(isset($post))
                        <fieldset class="form-inline" {{ Auth::user()->can('update', $post) ?: 'disabled' }}>
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-danger delete-post-button">Delete</button>
                        </fieldset>
                    @else
                        <button type="submit" class="btn btn-success">Publish Blogpost</button>
                    @endif

                </fieldset>
            </form>
            <form id="delete-media-form"
                  method="POST"
                  style="display:none">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}
            </form>
            @if (isset($post))
                <form id="delete-post"
                      action="{{ route('admin.botm.destroy', $post) }}"
                      method="POST"
                      style="display:none">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                </form>
            @endif
        </div>
    </div>
@endsection
