@extends('layouts.admin')

@section('content')
    <h1>
        Products
        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-outline-primary ml-1">Create new</a>
    </h1>
    <hr class="mb-4">

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}">{{ $product->title }}</a>
                    </td>
                    <td>
                        {{ $product->items }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-xs-center" colspan="3">
                        There are no products yet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
