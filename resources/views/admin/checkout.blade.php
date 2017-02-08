@extends('layouts.admin')

@section('content')
    <h1>Checkout</h1>
    <hr class="mb-4">

    <div class="row">
        <div class="col-md-5 mb-4">
            <h4>Summary</h4>
            <table class="table table-striped mb-4">
                <tr>
                    <th>Current balance</th>
                    <td>
                        <i class="fa fa-fw fa-heart"></i>
                        {{ $user->getBalance(\App\TransactionCurrency::HEARTS) }}
                    </td>
                </tr>
                <tr>
                    <th>Cart total</th>
                    <td>
                        <i class="fa fa-fw fa-heart"></i>
                        {{ $cartTotal }}
                    </td>
                </tr>
                <tr>
                    <th>Left after purchase</th>
                    <td>
                        <i class="fa fa-fw fa-heart"></i>
                        {{ $user->getBalance(\App\TransactionCurrency::HEARTS) - $cartTotal }}
                    </td>
                </tr>
            </table>
            <a href="{{ route('admin.purchase') }}" class="btn btn-lg btn-display btn-success">Place order</a>
        </div>
        <div class="col-md-6 offset-md-1 mb-4">
            <h4>Cart</h4>
            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart as $item)
                        <tr>
                            <td>
                                <img src="{{ Storage::url($item->product->media->first()->path) }}"
                                     height="54">
                            </td>
                            <td class="align-middle">
                                {{ $item->product->title }}
                            </td>
                            <td class="align-middle">
                                {{ $item->size }}
                            </td>
                            <td class="align-middle">
                                {{ $item->product->price }}
                            </td>
                            <td class="align-middle">
                                <a href="{{ route('admin.shop.remove-from-cart', ['user' => $user, 'index' => $loop->index]) }}">
                                    <i class="fa fa-fw fa-close text-danger"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
