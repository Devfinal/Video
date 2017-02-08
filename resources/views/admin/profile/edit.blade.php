@extends('layouts.admin')

@section('content')
    <h1>{{ $user->username }}</h1>
    <hr class="mb-4">

    <form method="POST" action="{{ route('admin.profile.update', $user) }}" class="row">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="col-md-3 mb-4">
            <label>Profile Image</label>
            @if (isset($user->profile->cover))
                <img src="{{ Storage::url($user->profile->cover->path) }}" class=" img-thumbnail img-fluid w-100">
            @endif
        </div>
        <div class="col-md-9 mb-4">
            {{-- ... --}}
        </div>
        <div class="col-xs-12 mb-4">
            <div class="form-group{{ $errors->has('presentation') ? ' has-danger' : '' }}">
                <label for="presentation" class="form-control-label">Presentation</label>
                <textarea id="presentation"
                          class="form-control{{ $errors->has('presentation') ? ' form-control-danger' : '' }} tinymce"
                          name="presentation">
                    {{ isset($user->profile->presentation) ? $user->profile->presentation : old('presentation') }}
                </textarea>

                @if ($errors->has('presentation'))
                    <span class="form-control-feedback">
                        {{ $errors->first('presentation') }}
                    </span>
                @endif
            </div>
        </div>
        <div class="col-xs-12">
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>
@endsection
