<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use App\Events\ProfileImageUploaded;
use App\Media;
use App\Post;
use App\TransactionCurrency;
use App\TransactionReason;
use App\User;
use Illuminate\Http\Request;

Auth::routes();
Route::get('/register/verify/{token}', 'Auth\RegisterController@verify');
Route::get('/register/payments/complete', 'Auth\RegisterController@paymentComplete')->name('payments.complete');
Route::get('/register/payments/canceled', 'Auth\RegisterController@paymentCanceled')->name('payments.canceled');
Route::post('/customers/{user}/subscription', 'SettingsController@storeSubscription')->name('customers.subscription.store');
Route::get('/customers/payments/complete', 'SettingsController@paymentComplete')->name('customers.payments.complete');
Route::get('/customers/payments/canceled', 'SettingsController@paymentCanceled')->name('customers.payments.canceled');

// model routes
Route::get('/models/register', 'Auth\RegisterModelController@showRegistrationForm');
Route::post('/models/register', 'Auth\RegisterModelController@register');
Route::get('/models/register/verify/{token}', 'Auth\RegisterModelController@verify');

// pages
Route::get('/', 'PagesController@index')->name('home');
Route::get('/babe-of-the-month', 'PagesController@girlOfTheWeek')->name('botm');
Route::get('/subscription-terms', 'PagesController@terms')->name('terms');
Route::get('/model-terms', 'PagesController@modelTerms')->name('models.terms');
Route::get('/privacy-policy', 'PagesController@privacyPolicy')->name('privacy-policy');
Route::get('/content-policy', 'PagesController@contentPolicy')->name('content-policy');
Route::get('/cookies', 'PagesController@cookies')->name('cookies');
Route::get('/contact', 'PagesController@contact')->name('contact');
Route::post('/contact', 'PagesController@contactSubmit')->name('contact-submit');

Route::get('/settings', 'SettingsController@edit')->middleware('auth')->name('settings.edit');
Route::put('/settings/{user}', 'SettingsController@update')->name('settings.update');

Route::resource('models', 'ModelsController', [
    'parameters' => [
        'models' => 'user'
    ],
    'only' => [
        'index',
        'show'
    ]
]);

Route::get('/models/{user}/posts/{post}', 'PostsController@show')->name('posts.show');
Route::get('/babe-of-the-month/{post}', 'GirlOfTheWeekController@show')->name('botm.show');

Route::put('/posts/{post}/like', function (Request $request, Post $post) {
    $user = Auth::user();

    if (!is_null($user) && $user->hasRole('admin')) {
        $post->user->transactions()->create([
            'currency' => TransactionCurrency::HEARTS,
            'amount' => 1,
            'reason' => TransactionReason::ADMIN_LIKE
        ]);
        $post->setMeta('admin_likes', $post->getMeta('admin_likes') + 1);

        return redirect()->back();
    }

    if (!$post->doesUserLike($user)) {
        $post->user->transactions()->create([
            'currency' => TransactionCurrency::HEARTS,
            'amount' => 1,
            'reason' => TransactionReason::CUSTOMER_LIKE
        ]);

        if (!is_null($user)) {
            $post->userLikes()->attach($user);
        } else {
            $post->addAnonymousLike($request->ip());
        }
    }

    return redirect()->back();
})->name('posts.like');

// admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin.panel']], function () {
    Route::put('/posts/{post}/media/sort', function (Request $request, Post $post) {
        if (count($request->get('order')) !== $post->media->count()) {
            throw new HttpInvalidParamException;
        }

        $post->setMeta('mediaOrder', $request->get('order'));
        $post->save();

        return response('', 200);
    });

    Route::delete('/posts/{post}/media/{media}', function (Post $post, Media $media) {
        $post->media()->detach($media->id);

        return redirect()->back();
    });

    Route::put('/posts/{post}/media/{media}', function (Request $request, Post $post, Media $media) {
        Validator::make($request->all(), [
            'protected' => 'required'
        ])->validate();

        $media->protected = (bool)$request->get('protected');
        $media->save();

        return redirect()->back();
    });

    Route::resource('posts', 'Admin\PostsController', [
        'except' => [
            'show'
        ],
        'names' => [
            'index' => 'admin.posts.index',
            'create' => 'admin.posts.create',
            'store' => 'admin.posts.store',
            'edit' => 'admin.posts.edit',
            'destroy' => 'admin.posts.destroy',
            'update' => 'admin.posts.update'
        ]
    ]);

    Route::delete('/botm/{post}/media/{media}', function (Post $post, Media $media) {
        $post->media()->detach($media->id);

        return redirect()->back();
    });

    Route::put('/botm/{post}/media/{media}', function (Request $request, Post $post, Media $media) {
        Validator::make($request->all(), [
            'protected' => 'required'
        ])->validate();

        $media->protected = (bool)$request->get('protected');
        $media->save();

        return redirect()->back();
    });

    Route::resource('botm', 'Admin\GirlOfTheWeekController', [
        'parameters' => [
            'botm' => 'post'
        ],
        'except' => [
            'show'
        ],
        'names' => [
            'index' => 'admin.botm.index',
            'create' => 'admin.botm.create',
            'store' => 'admin.botm.store',
            'edit' => 'admin.botm.edit',
            'destroy' => 'admin.botm.destroy',
            'update' => 'admin.botm.update'
        ]
    ]);

    Route::put('/models/{user}/activate', 'Admin\ModelsController@activate')->name('admin.models.activate');
    Route::put('/models/{user}/deactivate', 'Admin\ModelsController@deactivate')->name('admin.models.deactivate');
    Route::put('/models/{user}/profile', 'Admin\ModelsController@updateProfile')->name('admin.models.update-profile');
    Route::put('/models/{user}/balance', 'Admin\ModelsController@updateBalance')->name('admin.models.update-balance');
    Route::put('/models/{user}/settings',
        'Admin\ModelsController@updateSettings')->name('admin.models.update-settings');
    Route::resource('models', 'Admin\ModelsController', [
        'parameters' => [
            'models' => 'user'
        ],
        'except' => [
            'create',
            'store',
            'update'
        ],
        'names' => [
            'index' => 'admin.models.index',
            'show' => 'admin.models.show',
            'edit' => 'admin.models.edit',
            'destroy' => 'admin.models.destroy'
        ]
    ]);

    Route::put('/customers/{user}/balance', 'Admin\CustomerController@updateBalance')->name('admin.customers.update-balance');
    Route::delete('/customers/{user}/subscription', 'Admin\CustomerController@cancel')
        ->name('admin.customers.cancel');
    Route::resource('customers', 'Admin\CustomerController', [
        'parameters' => [
            'customers' => 'user'
        ],
        'only' => [
            'index',
            'edit',
            'update',
            'destroy'
        ],
        'names' => [
            'index' => 'admin.customers.index',
            'edit' => 'admin.customers.edit',
            'update' => 'admin.customers.update',
            'destroy' => 'admin.customers.destroy'
        ]
    ]);

    Route::put('/profiles/{user}/image', function (Request $request, User $user) {
        Validator::make($request->all(), [
            'image' => 'required',
            'image.file' => 'mimes:jpg'
        ])->validate();

        $file = $request->file('image');
        $image = Media::create([
            'type' => 'image',
            'mime_type' => $file->getMimeType(),
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path' => Storage::putFile("media/{$user->id}/profile-images", $file)
        ]);

        event(new ProfileImageUploaded($user, $image, $file));

        $user->profile->image()->associate($image);
        $user->profile->save();

        return redirect()->back();

    })->name('admin.profile.image');

    Route::delete('/profiles/{user}/image', function (Request $request, User $user) {
        $user->profile->image()->dissociate();
        $user->profile->save();

        return redirect()->back();
    });

    Route::put('/profiles/{user}/cover', function (Request $request, User $user) {
        Validator::make($request->all(), [
            'cover' => 'required',
            'cover.file' => 'mimes:jpg'
        ])->validate();

        $file = $request->file('cover');
        $cover = Media::create([
            'type' => 'image',
            'mime_type' => $file->getMimeType(),
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path' => Storage::putFile("media/{$user->id}/cover-images", $file)
        ]);

        $user->profile->cover()->associate($cover);
        $user->profile->save();

        return redirect()->back();
    })->name('admin.profile.cover');

    Route::delete('/profiles/{user}/cover', function (Request $request, User $user) {
        $user->profile->cover()->dissociate();
        $user->profile->save();

        return redirect()->back();
    });

    Route::put('/profiles/{user}/background', function (Request $request, User $user) {
        Validator::make($request->all(), [
            'background' => 'required',
            'background.file' => 'mimes:jpg'
        ])->validate();

        $file = $request->file('background');
        $background = Media::create([
            'type' => 'image',
            'mime_type' => $file->getMimeType(),
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path' => Storage::putFile("media/{$user->id}/background-images", $file)
        ]);

        $user->profile->background()->associate($background);
        $user->profile->save();

        return redirect()->back();
    })->name('admin.profile.background');

    Route::delete('/profiles/{user}/background', function (Request $request, User $user) {
        $user->profile->background()->dissociate();
        $user->profile->save();

        return redirect()->back();
    });

    // Background in admin panel

    Route::put('/settings/background', function (Request $request) {
        Validator::make($request->all(), [
            'background' => 'required',
            'background.file' => 'mimes:jpg'
        ])->validate();

        $file = $request->file('background');
        $background = Media::create([
            'type' => 'image',
            'mime_type' => $file->getMimeType(),
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path' => Storage::putFile("media", $file)
        ]);

        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $siteMeta = \App\Meta::where('key', 'site_meta')->first();
            $siteMeta->background()->associate($background);
            $siteMeta->save();
        }

        return redirect()->back();
    })->name('admin.settings.background');

    Route::delete('/settings/background/delete', function (Request $request) {

        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $siteMeta = \App\Meta::where('key', 'site_meta')->first();
            $siteMeta->background()->dissociate();
            $siteMeta->save();
        }

        return redirect()->back();
    })->name('admin.settings.background.delete');


    Route::resource('profiles', 'Admin\ProfilesController', [
        'parameters' => [
            'profiles' => 'user'
        ],
        'only' => [
            'edit',
            'update'
        ],
        'names' => [
            'edit' => 'admin.profile.edit',
            'update' => 'admin.profile.update'
        ]
    ]);

    Route::group(['middleware' => 'admin'], function () {
        Route::resource('products', 'Admin\ProductsController', [
            'except' => [
                'show'
            ],
            'names' => [
                'index' => 'admin.products.index',
                'create' => 'admin.products.create',
                'store' => 'admin.products.store',
                'edit' => 'admin.products.edit',
                'update' => 'admin.products.update',
                'destroy' => 'admin.products.destroy',
            ]
        ]);

        Route::put('/orders/{order}/ship', 'Admin\OrdersController@ship')->name('admin.orders.ship');
        Route::resource('orders', 'Admin\OrdersController', [
            'only' => [
                'index',
                'edit'
            ],
            'names' => [
                'index' => 'admin.orders.index',
                'edit' => 'admin.orders.edit',
            ]
        ]);
    });
    Route::put('/orders/{order}/cancel', 'Admin\OrdersController@cancel')->name('admin.orders.cancel');

    Route::get('/shop', 'AdminController@shop')->name('admin.shop');
    Route::get('/checkout', 'AdminController@checkout')->name('admin.checkout');
    Route::get('/purchase', 'AdminController@purchase')->name('admin.purchase');
    Route::get('/model-orders', 'AdminController@modelOrders')->name('admin.model-orders');
    Route::get('/model-orders/{order}', 'AdminController@modelEditOrder')->name('admin.model-orders.edit');
    Route::get('/shop/add-to-cart/{product}/{user}', 'AdminController@addToCart')->name('admin.shop.add-to-cart');
    Route::get('/shop/remove-from-cart/{user}/{index}', 'AdminController@removeFromCart')->name('admin.shop.remove-from-cart');
    Route::get('/settings', 'AdminController@settings')->name('admin.settings');
    Route::put('/settings', 'AdminController@updateSettings')->name('admin.settings.update');
    Route::get('/faq', 'AdminController@faq')->name('admin.faq');
    Route::put('/faq', 'AdminController@updateFaq')->name('admin.faq.update');
    Route::get('/reports', 'AdminController@reports')->name('admin.reports.index');
    Route::post('/reports', 'AdminController@generateReport')->name('admin.reports.generate');
    Route::get('/reports/download', 'AdminController@downloadReport')->name('admin.reports.download');
    Route::get('/', 'AdminController@index')->name('admin');
});
