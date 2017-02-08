@extends('layouts.admin')

@push('scripts-body')
<script>
    $('.add-size-button').on('click', function (event) {
        $(event.currentTarget).before(
            '<div class="form-group{{ $errors->has('sizes.0') ? ' has-danger' : '' }}">' +
                '<div class="input-group">' +
                    '<input type="text" name="sizes[]" class="form-control" value="" required>' +
                    '<div class="input-group-btn">' +
                        '<button type="button" class="btn btn-danger remove-size-button">&times;</button>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    });

    $(document).on('click', '.remove-size-button', function (event) {
        $(event.currentTarget).closest('.form-group').remove();
    });
</script>
@endpush
@section('content')
    <div class="card">
        <div class="card-header">
            {{ isset($product) ? 'Edit' : 'New' }} product
        </div>
        <div class="card-block">
            <form id="save-product"
                  method="POST"
                  action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
                  enctype="multipart/form-data"
                  novalidate>
                {{ csrf_field() }}

                @if(isset($product))
                    {{ method_field('PUT') }}
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('title') ? ' has-danger' : '' }}">
                            <label for="title" class="form-control-label">Title</label>
                            <input type="text"
                                   id="title"
                                   form="save-product"
                                   class="form-control{{ $errors->has('title') ? ' form-control-danger' : '' }}"
                                   name="title"
                                   value="{{ isset($product) ? $product->title : old('title') }}"
                                   required
                                   autocomplete="off">

                            @if ($errors->has('title'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('title') }}
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('price') ? ' has-danger' : '' }}">
                                    <label for="price" class="form-control-label">Price</label>
                                    <input type="number"
                                           min="0"
                                           id="price"
                                           class="form-control{{ $errors->has('price') ? ' form-control-danger' : '' }}"
                                           name="price"
                                           value="{{ isset($product) ? $product->price : old('price') }}"
                                           required
                                           autocomplete="off">

                                    @if ($errors->has('price'))
                                        <div class="form-control-feedback">
                                            {{ $errors->first('price') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('items') ? ' has-danger' : '' }}">
                                    <label for="items" class="form-control-label">Items</label>
                                    <input type="number"
                                           min="0"
                                           id="items"
                                           class="form-control{{ $errors->has('items') ? ' form-control-danger' : '' }}"
                                           name="items"
                                           value="{{ isset($product) ? $product->items : old('items') }}"
                                           required
                                           autocomplete="off">

                                    @if ($errors->has('items'))
                                        <div class="form-control-feedback">
                                            {{ $errors->first('items') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Sizes</label>
                                @if (isset($product) && !is_null($product->getMeta('sizes')) || !is_null(old('sizes')))
                                    @foreach (old('sizes') ?: $product->getMeta('sizes', []) as $size)
                                        <div class="form-group{{ $errors->has("sizes.{$loop->index}") ? ' has-danger' : '' }}">
                                            <div class="input-group">
                                                <input type="text"
                                                       name="sizes[]"
                                                       class="form-control{{ $errors->has("sizes.{$loop->index}") ? ' has-danger' : '' }}"
                                                       value="{{ $size }}"
                                                       required>
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-danger remove-size-button">&times;</button>
                                                </div>
                                            </div>

                                            @if ($errors->has("sizes.{$loop->index}"))
                                                <div class="form-control-feedback">
                                                    Size cannot be empty
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                                <button type="button" class="btn btn-success float-xs-right add-size-button">Add</button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('article_nr') ? ' has-danger' : '' }}">
                                    <label for="article_nr" class="form-control-label">Article nr.</label>
                                    <input type="text"
                                           id="article_nr"
                                           class="form-control{{ $errors->has('article_nr') ? ' form-control-danger' : '' }}"
                                           name="article_nr"
                                           value="{{ isset($product) ? $product->getMeta('article_nr') : old('article_nr') }}"
                                           required
                                           autocomplete="off">

                                    @if ($errors->has('article_nr'))
                                        <div class="form-control-feedback">
                                            {{ $errors->first('article_nr') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 offset-md-3">
                        <div class="form-group{{ $errors->has('media') || $errors->has('media.0.file') ? ' has-danger' : '' }}">
                            <label class="form-control-label">Picture</label>

                            @if (isset($product) && $product->media->first() !== null)
                                <img src="{{ Storage::url($product->media->first()->getThumbnail()->path) }}"
                                     class="img-thumbnail img-fluid w-100 mb-2">
                            @endif

                            <input type="file"
                                   id="media"
                                   name="media[][file]"
                                   class="form-control media-upload"
                                   multiple
                                   accept="image/*">

                            @if ($errors->has('media'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('media') }}
                                </div>
                            @endif

                            @if ($errors->has('media.0.file'))
                                <div class="form-control-feedback">
                                    {{ $errors->first('media.0.file') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('description') ? ' has-danger' : '' }} mb-4">
                    <label for="description" class="form-control-label">Description</label>
                    <textarea id="description"
                              form="save-product"
                              class="form-control{{ $errors->has('description') ? ' form-control-danger' : '' }} tinymce"
                              name="description"
                              required>{{ isset($product) ? $product->description : old('description') }}</textarea>

                    @if ($errors->has('description'))
                        <span class="form-control-feedback">
                            {{ $errors->first('description') }}
                        </span>
                    @endif
                </div>

                @if(isset($product))
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-danger delete-post-button">Delete</button>
                @else
                    <button type="submit" form="save-product" class="btn btn-success">Create product</button>
                @endif
            </form>

            @if (isset($product))
                <form id="delete-post"
                      action="{{ route('admin.products.destroy', $product) }}"
                      method="POST"
                      style="display:none">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                </form>
            @endif
        </div>
    </div>
@endsection
