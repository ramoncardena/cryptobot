<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

       
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="application-name" content="CryptoBot">
        <meta name="apple-mobile-web-app-title" content="CryptoBot">
        <meta name="msapplication-starturl" content="/">
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
       

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CryptoBot') }} [Alpha]</title>

        <!-- Styles -->
        <link href="node_modules/open-iconic/font/css/open-iconic-foundation.css" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/datatables.css') }}" rel="stylesheet">
        <link href="{{ asset('css/vendor.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="loader"></div>
        <div class="off-canvas-wrapper">
            <div class="off-canvas position-right" id="offCanvas" data-off-canvas data-auto-focus="false">

                <!-- Menu -->
                <div class="menu-title text-center">
                    <img class="logo" width="60" src="<?php echo Storage::url('cryptobot-logo-white-200px-new.png')?>"/>
                    <div class="h4">CryptoBot</div>
                    <div class="version">{{ config('app.version', '0.0.0') }} [Alpha]</div>
                    <!--asset('storage/cryptobot-logo-white-200px.png') -->
                </div>
                <ul class="vertical menu text-center">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li><a href="/portfolio">Portfolio</a></li>
                    <li><a href="/trades">Trades</a></li>
                    <li><a href="/connections">Exchange APIs</a></li>
                    <li><a href="/settings">Settings</a></li>
                    <li><a href="/invite">Invitations</a></li>
                    <li>-</li>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">Support</a></li>
                    <li>-</li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>

            </div>
            <div class="off-canvas-content" data-off-canvas-content>
                <!-- Your page content lives here -->

                <div id="app">
                    <nav class="navigation">

                        <div class="title-bar" data-responsive-toggle="example-menu" data-hide-for="medium">
                            <div class="title-bar-left">
                                <button class="menu-icon" type="button" data-toggle="example-menu"></button>
                            </div>
                            <div class="title-bar-center text-center">
                                <img class="logo" width="60" src="<?php echo Storage::url('cryptobot-logo-white-200px.png')?>"/>
                                <p class="h2">CryptoBot</p>
                            </div>

                            <div class="title-bar-right">
                                @if (Auth::check())
                                <a href="#" data-open="notificationsModal"><div class="icon icons8-Bell"></div><span class="badge alert">{{ count(Auth::user()->Notifications) }}</span></a>
                                @endif
                                <button class="menu-icon" type="button" data-open="offCanvas"></button>
                            </div>
                        </div>

                        <div class="top-bar" id="example-menu">

                            <div class="top-bar-left show-for-medium">
                                <ul class="menu">
                                    <li><img class="logo" src="<?php echo asset('storage/cryptobot-logo-40px-new-white.png') ?>"/> <span class="nav-title">CryptoBot</span></li>
                                </ul>
                            </div>

                            <div class="top-bar-right">
                                <ul class="dropdown menu" data-dropdown-menu data-disable-hover="true" data-click-open="true">
                                    @if (Auth::guest())
                                        <li><a href="{{ route('login') }}">Login</i></a></li>
                                        <li><a href="{{ route('register') }}">Register</a></li>
                                    @else
                                        <li> <a href="{{ route('home') }}" title="Home">  <div class="icon icons8-Home"></div> </a></li>
                                        <li> <a href="{{ route('dashboard') }}" title="Dashboard"> <div class="icon icons8-Speedometer"></div></a></li>
                                        <li> <a href="{{ route('portfolio') }}" title="Portfolio">  <div class="icon icons8-Pie-Chart"></div></a></li>
                                        <li> <a href="{{ route('trades') }}" title="Trades">  <div class="icon icons8-Banknotes"></div></a></li>
                                        <li><a href="{{ route('settings') }}" title="Settings" ><div class="icon icons8-Settings"></div></a></li>
                                        <li  class="show-for-medium">
                                            <a href="#" data-open="notificationsModal" title="Alerts"> <div class="icon icons8-Bell"></div><span class="badge alert">{{ count(Auth::user()->Notifications) }}</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}" title="Logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                                <!-- <i class="fa fa-sign-out" aria-hidden="true"></i> -->
                                                <div class="icon icons8-Log-Out"></div>
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                        <li class="hide-for-small-only">
                                            <a href="#" title="Menu"><div class="icon icons8-Menu" data-toggle="offCanvas"></div></i></a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                    </nav>

                    @yield('content')
                    @if (Auth::check())
                         @include('partials.notifications')
                    @endif

                </div>

                <!-- Scripts -->
                <script src="{{ asset('js/app.js') }}"></script>
                <script>
                    $(document).foundation();

                    $(document).ready(function() {
                        $(".loader").fadeOut("slow");
                    });
                </script>


            </div>
        </div>
    </body>
</html>
