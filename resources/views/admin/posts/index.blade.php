@extends('layouts.admin')

@section('content')
    <h1>
        Posts
        <a href="{{ route('admin.posts.create') }}" class="btn btn-sm btn-outline-primary ml-1">Create new</a>
    </h1>
    <hr class="mb-4">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Updated</th>
                <th><i class="fa fa-heart"></i></th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
                <tr>
                    <td>
                        <a href="{{ route('admin.posts.edit', $post->id) }}">{{ $post->title }}</a>
                    </td>
                    <td>
                        {{ $post->updated_at->format('Y-m-d') }}
                    </td>
                    <td>
                        {{ $post->getLikesCount() }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-xs-center" colspan="3">
                        There are no posts yet
                        <a href="{{ route('admin.posts.create') }}" class="btn btn-sm btn-outline-primary ml-1">create one!</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
