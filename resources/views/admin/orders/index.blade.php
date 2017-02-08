@extends('layouts.admin')

@section('content')
    <h1>
        Orders
    </h1>
    <hr class="mb-4">

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Model</th>
                <th>Items</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.edit', $order) }}">{{ $order->id }}</a>
                    </td>
                    <td>
                        {{ $order->user->username }}
                    </td>
                    <td>
                        {{ $order->products->count() }}
                    </td>
                    <td>
                        <i class="fa fa-fw fa-heart"></i>
                        {{ $order->getAmount() }}
                    </td>
                    <td class="text-{{ $order->status === \App\OrderStatus::SHIPPED ? 'success' : ($order->status === \App\OrderStatus::CANCELED ? 'danger' : 'warning') }}">
                        {{ \App\OrderStatus::getString($order->status) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-xs-center" colspan="5">
                        There are no orders yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <nav class="text-xs-center">
        {{ $orders->links('vendor.pagination.bootstrap-4') }}
    </nav>
@endsection
