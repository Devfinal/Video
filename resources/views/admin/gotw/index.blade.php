@extends('layouts.admin')

@section('content')
    <h1>
        Posts
        <a href="{{ route('admin.botm.create') }}" class="btn btn-sm btn-outline-primary ml-1">Create new</a>
    </h1>
    <hr>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Published</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
                <tr>
                    <td>
                        <a href="{{ route('admin.botm.edit', $post->id) }}">{{ $post->title }}</a>
                    </td>
                    <td>
                        {{ $post->created_at->format('Y-m-d') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-xs-center" colspan="2">
                        There are no posts yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
