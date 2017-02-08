@extends('layouts.admin')

@section('content')
    @if ($order->status === \App\OrderStatus::PROCESSING)
        <div class="float-xs-right">
            <button type="submit" form="cancel-order-form" class="btn btn-danger">Cancel</button>
            <button type="submit" form="ship-order-form" class="btn btn-success">Done</button>
        </div>
    @endif

    <h1>
        Order: {{ $order->id }}
        @if ($order->status !== \App\OrderStatus::PROCESSING)
            <small class="text-muted">({{ \App\OrderStatus::getString($order->status) }})</small>
        @endif
    </h1>
    <hr class="mb-4">

    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Products</h4>
            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->products as $product)
                        <tr>
                            <td>
                                <img src="{{ Storage::url($product->media->first()->path) }}"
                                     height="54">
                            </td>
                            <td class="align-middle">
                                {{ $product->title }}
                            </td>
                            <td class="align-middle">
                                <i class="fa fa-fw fa-heart"></i>
                                {{ $product->price }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h4>Note</h4>
            <pre>{{ $order->note }}</pre>
        </div>
    </div>

    @if ($order->status === \App\OrderStatus::PROCESSING)
        <form id="ship-order-form" method="POST" action="{{ route('admin.orders.ship', $order) }}">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
        </form>
        <form id="cancel-order-form" method="POST" action="{{ route('admin.orders.cancel', $order) }}">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
        </form>
    @endif
@endsection
