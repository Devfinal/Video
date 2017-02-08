@extends('layouts.admin')

@section('content')
    <h1>
        Models
    </h1>
    <hr>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Username</th>
                <th>Date</th>
                <th>Active</th>
            </tr>
        </thead>
        <tbody>
            @forelse($models as $model)
                <tr>
                    <td>
                        <a href="{{ route('admin.models.edit', $model->id) }}">{{ $model->username }}</a>
                    </td>
                    <td>
                        {{ $model->created_at->format('Y-m-d') }}
                    </td>
                    <td>
                        <i class="fa fa-{{ $model->getMeta('deactivated', false) ? 'minus text-warning' : ($model->active ? 'check text-success' : 'times text-danger') }}"></i>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-xs-center" colspan="3">
                        There are no models yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <nav class="text-xs-center">
        {{ $models->links('vendor.pagination.bootstrap-4') }}
    </nav>
@endsection
