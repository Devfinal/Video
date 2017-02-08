@extends('layouts.main')

@section('content')
    @if (!is_null($user->profile->cover))
        <header class="card profile-header">
            <a href="{{ route('models.show', $user) }}">
                <img src="{{ Storage::url($user->profile->cover->path) }}"
                     class="card-img-top img-fluid w-100">
            </a>
        </header>
    @endif

    <div class="row">
        <div class="col-lg-12">
            @yield('model-content')
        </div>
    </div>
@endsection
