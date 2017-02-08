<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode'
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            'App\Http\Middleware\EncryptCookies',
            'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
            'Illuminate\Session\Middleware\StartSession',
            'Illuminate\View\Middleware\ShareErrorsFromSession',
            'App\Http\Middleware\VerifyCsrfToken',
            'Illuminate\Routing\Middleware\SubstituteBindings',
            'Laravel\Passport\Http\Middleware\CreateFreshApiToken'
        ],

        'api' => [
            'throttle:60,1',
            'bindings'
        ]
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => 'Illuminate\Auth\Middleware\Authenticate',
        'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'bindings' => 'Illuminate\Routing\Middleware\SubstituteBindings',
        'can' => 'Illuminate\Auth\Middleware\Authorize',
        'guest' => 'App\Http\Middleware\RedirectIfAuthenticated',
        'throttle' => 'Illuminate\Routing\Middleware\ThrottleRequests',
        'admin' => 'App\Http\Middleware\AdminMiddleware',
        'admin.panel' => 'App\Http\Middleware\AdminPanelMiddleware'
    ];
}
