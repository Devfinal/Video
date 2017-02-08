<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="keywords" content="modellbyrå,naken,norske jenter,norska tjejer,glamour,nude girls,big tits,private movies,private pictures,store pupper,norwegian girls,lesbian,teen,tenåring,sexy ass,big ass,sexy,milf,sex movie,massage" />
        <meta name="description" content="Welcome to beautiesfromheaven.com that offers you sexy blogs with private pictures and videos from local girls all over the world." />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '') }}</title>

        <!-- Styles -->
        <link href="/css/main.css" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
              rel="stylesheet"
              integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN"
              crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/plyr/2.0.11/plyr.css">


        @if($background = \App\Meta::where('key', 'site_meta')->first()->background)
            <style>
                @media screen and (min-width: 1200px) {
                    body {
                        background-image: url({{ Storage::url($background->path) }});
                    }
                }
            </style>
        @endif

        @stack('styles')

        <!-- Scripts -->
        <script>
            window.Laravel = <?= json_encode(['csrfToken' => csrf_token(),]); ?>
        </script>
    </head>
    <body>
        <nav class="navbar main-header navbar-fixed-top navbar-dark bg-inverse">
            <div class="container">
                @if (!Auth::check())
                <a href="{{ url('/register' . (isset($registerForModel) ? '?' . http_build_query(['model' => $registerForModel]) : '')) }}"
                   class="navbar-brand text-primary">Get Full Access</a>
                @endif
                <ul class="nav navbar-nav float-xs-right">
                    <li class="nav-item dropdown">
                        <button type="button" class="navbar-toggler" data-toggle="dropdown"></button>
                        <div class="dropdown-menu dropdown-menu-right multi-level">
                            <a href="{{ route('botm') }}"
                               class="dropdown-item text-light{{ strpos(Route::currentRouteName(), 'botm') === 0 ? ' active' : '' }}">
                                Babe of the Month
                            </a>
                            <a href="{{ route('models.index') }}"
                               class="dropdown-item text-light{{ strpos(Route::currentRouteName(), 'models.index') === 0 ? ' active' : '' }}">
                                All models
                            </a>
                            <div class="dropdown-divider"></div>
                            @if (Auth::guest())
                            <a class="dropdown-item text-light{{ Request::is('register') ? ' active' : '' }}"
                               href="{{ url('/register') }}">
                                Get full access
                            </a>
                            <a class="dropdown-item text-light{{ Request::is('models/register') ? ' active' : '' }}"
                               href="{{ url('/models/register') }}">
                                Become a model
                            </a>
                            <a class="dropdown-item text-light{{ Request::is('login') ? ' active' : '' }}"
                               href="{{ route('login') }}">
                                Login
                            </a>
                            @else
                            @if (Auth::user()->hasRole('customer'))
                            <a href="{{ route('settings.edit') }}"
                               class="dropdown-item text-light{{ Request::is('settings') ? ' active' : '' }}">
                                <i class="fa fa-cog fa-fw"></i>
                                Settings
                            </a>
                            @endif
                            @if (Auth::user()->hasRole('model'))
                            <a href="{{ route('admin.posts.create') }}" class="dropdown-item text-light py-3">
                                <i class="fa fa-fw fa-plus-circle text-success"></i>
                                Create New Blogpost
                            </a>
                            <a href="{{ route('admin') }}" class="dropdown-item text-light">
                                <i class="fa fa-tachometer fa-fw"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.shop') }}" class="dropdown-item text-light">
                                <i class="fa fa-shopping-cart fa-fw"></i>
                                Shop
                            </a>
                            @endif
                            @if (Auth::user()->hasRole('admin'))
                            <a href="{{ route('admin') }}" class="dropdown-item text-light">
                                <i class="fa fa-tachometer fa-fw"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.shop') }}" class="dropdown-item text-light">
                                <i class="fa fa-shopping-cart fa-fw"></i>
                                Shop
                            </a>
                            <a href="{{ route('admin.settings') }}" class="dropdown-item text-light">
                                <i class="fa fa-cog fa-fw"></i>
                                Settings
                            </a>
                            <a href="{{ route('admin.models.index') }}" class="dropdown-item text-light">
                                <i class="fa fa-user-circle fa-fw"></i>
                                Models
                            </a>
                            <a href="{{ route('admin.customers.index') }}" class="dropdown-item text-light">
                                <i class="fa fa-user fa-fw"></i>
                                Customers
                            </a>
                            <a href="{{ route('admin.botm.index') }}" class="dropdown-item text-light">
                                <i class="fa fa-thumb-tack fa-fw"></i>
                                Babe of the Month
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="dropdown-item text-light">
                                <i class="fa fa-shopping-bag fa-fw"></i>
                                Products
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="dropdown-item text-light">
                                <i class="fa fa-truck fa-fw"></i>
                                Orders
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="dropdown-item text-light">
                                <i class="fa fa-file-text fa-fw"></i>
                                Reports
                            </a>
                            <a href="{{ route('admin.faq') }}" class="dropdown-item text-light">
                                <i class="fa fa-question-circle fa-fw"></i>
                                FAQ
                            </a>
                            @elseif (Auth::user()->hasRole('model'))
                            <a href="{{ route('admin.model-orders') }}" class="dropdown-item text-light">
                                <i class="fa fa-truck fa-fw"></i>
                                Orders
                            </a>
                            <a href="{{ route('admin.profile.edit', Auth::user()) }}"
                               class="dropdown-item text-light">
                                <i class="fa fa-user-circle fa-fw"></i>
                                Profile
                            </a>
                            <a href="{{ route('admin.posts.index') }}" class="dropdown-item text-light">
                                <i class="fa fa-thumb-tack fa-fw"></i>
                                My Posts
                            </a>
                            <a href="{{ route('admin.settings') }}" class="dropdown-item text-light">
                                <i class="fa fa-cog fa-fw"></i>
                                Settings
                            </a>
                            <a href="{{ route('admin.faq') }}" class="dropdown-item text-light">
                                <i class="fa fa-question-circle fa-fw"></i>
                                FAQ
                            </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item text-light"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                Logout
                            </a>

                            <form id="logout-form"
                                  action="{{ route('logout') }}"
                                  method="POST"
                                  style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="header-spacer"></div>

        <div class="main-header-sub container">
            <a href="{{ route('home') }}" class="d-inline-block">
                <img src="/images/logo-full.png" class="main-logo">
            </a>
            {{--<img src="/images/plug.png" class="main-plug flex-xs-bottom">--}}
            @if (!Auth::check())
            <a href="{{ url('/register' . (isset($registerForModel) ? '?' . http_build_query(['model' => $registerForModel]) : '')) }}"
               class="btn btn-primary btn-display">
                Get Full Access
            </a>
            @endif
        </div>

        <div class="main-content container mb-4">
            @if (Session::has('message'))
            <div class="alert alert-info">
                {{ Session::get('message') }}
            </div>
            @endif

            @yield('content')
        </div>

        <footer class="bg-inverse p-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('terms') }}">Subscription terms</a>
                            </li>
                            <li>
                                <a href="{{ route('privacy-policy') }}">Privacy policy</a>
                            </li>
                            <li>
                                <a href="{{ route('content-policy') }}">Content policy</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('cookies') }}">Cookies</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('contact') }}">Help & Contact</a>
                            </li>
                            <li>
                                <a href="{{ url('/models/register') }}">Become a model</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="pswp__bg"></div>
            <div class="pswp__scroll-wrap">
                <div class="pswp__container">
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                </div>
                <div class="pswp__ui pswp__ui--hidden">
                    <div class="pswp__top-bar">
                        <div class="pswp__counter"></div>
                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                        <button class="pswp__button pswp__button--share" title="Share"></button>
                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                        <div class="pswp__preloader">
                            <div class="pswp__preloader__icn">
                                <div class="pswp__preloader__cut">
                                    <div class="pswp__preloader__donut"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                        <div class="pswp__share-tooltip"></div>
                    </div>
                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                    <div class="pswp__caption">
                        <div class="pswp__caption__center"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://unpkg.com/masonry-layout@4.1/dist/masonry.pkgd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.0/js/swiper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/2.0.11/plyr.js"></script>
        <script src="/js/main.js"></script>

        <!-- Google analytics -->
        <script>
                                   (function (i, s, o, g, r, a, m) {
                                       i['GoogleAnalyticsObject'] = r;
                                       i[r] = i[r] || function () {
                                           (i[r].q = i[r].q || []).push(arguments)
                                       }, i[r].l = 1 * new Date();
                                       a = s.createElement(o),
                                           m = s.getElementsByTagName(o)[0];
                                       a.async = 1;
                                       a.src = g;
                                       m.parentNode.insertBefore(a, m)
                                   })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

                                   ga('create', 'UA-90726882-1', 'auto');
                                   ga('send', 'pageview');
        </script>

        @stack('scripts-body')
    </body>
</html>
