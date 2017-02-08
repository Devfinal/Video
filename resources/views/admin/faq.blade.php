@extends('layouts.admin')

@section('content')
    <h1>FAQ</h1>
    <hr class="mb-4">

    @if ($user->hasRole('admin'))
        <form method="POST" action="{{ route('admin.faq.update') }}" novalidate>
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            <div class="form-group mb-4">
                <textarea id="content"
                          class="form-control tinymce"
                          name="content">{{ $content }}</textarea>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
        </form>
    @else
        <div class="container">
            {!! $content !!}
        </div>
    @endif
@endsection
