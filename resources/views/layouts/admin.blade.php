<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="robots" content="noindex">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | Admin</title>

    <!-- Styles -->
    <link href="/css/admin.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          rel="stylesheet"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/plyr/2.0.11/plyr.css">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
                'userRoles' => !is_null(Auth::user()) ? Auth::user()->roles()->pluck('name') : []
        ]); ?>
    </script>

    @stack('scripts-head')
</head>
<body class="sidebar-visible navbar-visible main-content-visible{{ isset($_COOKIE['sidebar-collapsed']) && $_COOKIE['sidebar-collapsed'] ? ' sidebar-collapsed' : '' }}{{ Session::has('sidebar-open') ? ' sidebar-open' : '' }}">
    <div class="blur-wrapper">

        {{-- Sidebar --}}
        <nav class="sidebar navbar-dark bg-inverse">
            <div class="sidebar-header">
                <a href="{{ route('home') }}" class="navbar-brand p-2 m-0">
                    <img src="/images/watermark.png" class="logo img-fluid">
                </a>
            </div>
            <div class="sidebar-content">
                <div class="navbar">
                    <ul class="navbar-nav nav">

                        <li class="nav-item">
                            <a href="{{ route('admin') }}"
                               class="nav-link{{ Request::is('admin') ? ' active' : '' }}">
                                <i class="fa fa-tachometer fa-fw"></i>
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.shop') }}"
                               class="nav-link{{ strpos(Route::currentRouteName(), 'admin.shop') === 0 ? ' active' : '' }}">
                                <i class="fa fa-shopping-cart fa-fw"></i>
                                Shop
                            </a>
                        </li>

                        @if (Auth::user()->hasRole('model'))
                            <li class="nav-item">
                                <a href="{{ route('admin.model-orders') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.model-orders') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-truck fa-fw"></i>
                                    Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.profile.edit', Auth::user()) }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.profile') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-user-circle fa-fw"></i>
                                    Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.posts.index') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.posts') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-thumb-tack fa-fw"></i>
                                    My Posts
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.settings') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.settings') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-cog fa-fw"></i>
                                    Settings
                                </a>
                            </li>
                        @endif

                        @if (Auth::user()->hasRole('admin'))
                            <li class="nav-item">
                                <a href="{{ route('admin.settings') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.settings') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-cog fa-fw"></i>
                                    Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.models.index') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.models') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-user-circle fa-fw"></i>
                                    Models
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.customers.index') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.customers') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-user fa-fw"></i>
                                    Customers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.botm.index') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.botm') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-thumb-tack fa-fw"></i>
                                    Babe of the Month
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.products.index') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.products') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-shopping-bag fa-fw"></i>
                                    Products
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.orders.index') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.orders') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-truck fa-fw"></i>
                                    Orders
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.index') }}"
                                   class="nav-link{{ strpos(Route::currentRouteName(), 'admin.reports') === 0 ? ' active' : '' }}">
                                    <i class="fa fa-file-text fa-fw"></i>
                                    Reports
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ route('admin.faq') }}"
                               class="nav-link{{ strpos(Route::currentRouteName(), 'admin.faq') === 0 ? ' active' : '' }}">
                                <i class="fa fa-question-circle fa-fw"></i>
                                FAQ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('home') }}"
                               class="nav-link">
                                <i class="fa fa-home fa-fw"></i>
                                Startpage
                            </a>
                        </li>

                    </ul>
                </div>

                @if (strpos(Route::currentRouteName(), 'admin.checkout') === false && Auth::check() && !is_null(Auth::user()->getMeta('cart')))
                    <div class="cart card mt-4 mx-1">
                        <div class="card-header">
                            <i class="fa fa-fw fa-shopping-cart"></i>
                            Cart
                        </div>
                        <ul class="cart-items list-group list-group-flush">
                            @foreach (Auth::user()->getMeta('cart') as $item)
                                <li class="list-group-item">
                                    <a href="{{ route('admin.shop.remove-from-cart', ['user' => Auth::user(), 'index' => $loop->index]) }}"
                                       class="float-xs-right">
                                        <i class="fa fa-fw fa-close text-danger"></i>
                                    </a>
                                    <div class="float-xs-right mr-4">
                                        <i class="fa fa-fw fa-heart"></i>
                                        {{ $item->product->price }}
                                    </div>
                                    <a href="#cart-item-{{ $loop->index }}" class="dropdown-toggle" data-toggle="collapse">
                                        {{ $item->product->title }}
                                    </a>
                                    <div id="cart-item-{{ $loop->index }}" class="collapse">
                                        <div class="media p-2">
                                            <div class="media-left">
                                                <img src="{{ Storage::url($item->product->media->first()->path) }}"
                                                     class="media-object"
                                                     width="54">
                                            </div>
                                            <div class="media-body">
                                                <div style="font-size:12px">
                                                    <label class="mb-0">Article nr</label>: {{ $item->product->getMeta('article_nr') }}
                                                    <br>
                                                    <label class="mb-0">Size</label>: {{ $item->size }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-footer">
                            <div class="float-xs-right">
                                <a href="{{ route('admin.checkout') }}" class="btn btn-sm btn-success">Checkout</a>
                            </div>
                            <i class="fa fa-fw fa-heart"></i> {{ Auth::user()->getMeta('cart')->sum(function ($item) { return $item->product->price; }) }}
                        </div>
                        <a href="#" class="link-wrapper sidebar-collapse-button"></a>
                    </div>
                @endif
            </div>
            <div class="sidebar-footer">
                <div class="navbar-nav">
                    <a href="#" class="nav-link sidebar-collapse-button"></a>
                </div>
            </div>
        </nav>

        {{-- Navbar --}}
        <nav class="navbar navbar-fixed-top navbar-horizontal navbar-dark bg-inverse">
            <button type="button" class="navbar-toggler hidden-sm-up"></button>
            <ul class="navbar-nav nav float-xs-right">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button">
                        <i class="fa fa-user-circle-o"></i>
                        {{ Auth::user()->username }} <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li class="dropdown-item">
                            <a href="{{ url('/logout') }}" class="nav-link"
                               onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        {{-- Main content --}}
        <div class="container-fluid main-content">
            @if (Session::has('message'))
                <div class="alert alert-info alert-dismissable fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {!! Session::get('message') !!}
                </div>
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissable fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {!! Session::get('success') !!}
                </div>
            @endif
            @if (Session::has('warning'))
                <div class="alert alert-warning alert-dismissable fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {!! Session::get('warning') !!}
                </div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-danger alert-dismissable fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {!! Session::get('error') !!}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <div id="progress-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-xs-center">Uploading video&hellip;</div>
                    <progress class="progress progress-success" value="0" max="100"></progress>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/2.0.11/plyr.js"></script>
    <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
    <script src="/js/tinymce-smileys.js"></script>
    <script src="/js/admin.js"></script>

    @stack('scripts-body')
</body>
</html>
