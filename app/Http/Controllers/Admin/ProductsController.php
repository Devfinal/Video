<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Media;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Storage;
use View;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('admin.products.index')
            ->with('products', Product::orderBy('created_at', 'desc')->paginate(25));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View::make('admin.products.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'media.*.file' => 'mimes:jpg,jpeg,png',
            'description' => 'required',
            'price' => 'required|numeric',
            'items' => 'required|numeric',
            'sizes' => 'array',
            'sizes.*' => 'string|filled'
        ]);

        $product = Product::create($request->all());
        $product->setMeta('sizes', $request->input('sizes'));
        $product->setMeta('article_nr', $request->input('article_nr'));

        if ($request->hasFile('media.*.file')) {
            $product->media()->saveMany(collect($request->file('media.*.file'))->map(function (
                UploadedFile $file
            ) use ($product, $request) {
                list($width, $height) = getimagesize($file);

                $media = new Media([
                    'type' => 'image',
                    'mime_type' => $file->getMimeType(),
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'path' => Storage::putFile("media/{$product->id}", $file),
                    'width' => $width,
                    'height' => $height,
                    'protected' => false
                ]);

                return $media;
            })->all());
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Successfully created product');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return View::make('admin.products.edit')
            ->with(compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->validate($request, [
            'title' => 'required',
            'article_nr' => 'required|string|filled',
            'media.*.file' => 'mimes:jpg,jpeg,png',
            'description' => 'required',
            'price' => 'required|numeric',
            'items' => 'required|numeric',
            'sizes' => 'array',
            'sizes.*' => 'string|filled'
        ]);

        $product->update($request->except('sizes'));
        $product->setMeta('sizes', $request->input('sizes'));
        $product->setMeta('article_nr', $request->input('article_nr'));

        if ($request->hasFile('media.*.file')) {
            $product->media()->detach();
            $product->media()->saveMany(collect($request->file('media.*.file'))->map(function (
                UploadedFile $file
            ) use ($product, $request) {
                list($width, $height) = getimagesize($file);

                $media = new Media([
                    'type' => 'image',
                    'mime_type' => $file->getMimeType(),
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'path' => Storage::putFile("media/{$product->id}", $file),
                    'width' => $width,
                    'height' => $height,
                    'protected' => false
                ]);

                return $media;
            })->all());
        }

        return redirect()
            ->back()
            ->with('success', 'Successfully updated product');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Successfully removed product');
    }
}
