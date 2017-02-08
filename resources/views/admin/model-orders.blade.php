@extends('layouts.admin')

@section('content')
    <h1>Orders</h1>
    <hr class="mb-4">

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Items</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($user->orders()->orderBy('created_at', 'desc')->take(10)->get() as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.model-orders.edit', $order) }}">{{ $order->id }}</a>
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
@endsection
