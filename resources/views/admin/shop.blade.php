@extends('layouts.admin')

@section('content')
    <h1>Shop</h1>
    <hr class="mb-4">

        <div class="product-list card-columns">
            @foreach ($products as $product)
                <div class="card">
                <form method="GET"
                      action="{{ route('admin.shop.add-to-cart', compact('product', 'user')) }}">
                    <div class="card-header p-1">
                        <label class="text-primary float-xs-left m-0">
                            <i class="fa fa-fw fa-heart"></i>
                            {{ sprintf('%d', $product->price) }}
                        </label>
                        <button type="submit"
                                class="btn btn-sm btn-primary float-xs-right">
                            Add
                        </button>
                    </div>
                    @if (!is_null($product->media->first()))
                        <img src="{{ Storage::url($product->media->first()->path) }}"
                             class="card-img-top img-fluid w-100">
                    @endif
                    <div class="card-block">
                        <h6 class="card-title">{{ $product->title }}</h6>
                        <div class="card-text" style="font-size:14px">{!! $product->description !!}</div>

                        @if (!is_null($product->getMeta('sizes')))
                            <div class="form-group">
                                <label for="size" class="form-control-label d-block">Size</label>
                                <select id="size" name="size" class="custom-select w-100">
                                    @foreach ($product->getMeta('sizes') as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <p class="text-muted mb-0">
                            ({{ $product->items }} left)
                        </p>
                    </div>
                </form>
                </div>
            @endforeach
        </div>
@endsection
