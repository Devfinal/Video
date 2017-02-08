@extends('layouts.admin')

@section('content')
    <h1>
        Customers
    </h1>
    <hr>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Created</th>
                <th>Subscription</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>
                        <a href="{{ route('admin.customers.edit', $customer) }}">{{ $customer->id }}</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.customers.edit', $customer) }}">{{ $customer->username }}</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.customers.edit', $customer) }}">{{ $customer->email }}</a>
                    </td>
                    <td>
                        {{ $customer->created_at->format('Y-m-d') }}
                    </td>
                    <td>
                        <i class="fa fa-{{ $customer->hasGracePeriod() ? 'minus text-warning' : ($customer->hasActiveSubscription() ? 'check text-success' : 'times text-danger') }}"></i>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-xs-center" colspan="5">
                        There are no customers yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <nav class="text-xs-center">
        {{ $customers->links('vendor.pagination.bootstrap-4') }}
    </nav>
@endsection
