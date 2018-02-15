<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        
        <link rel="manifest" href="manifest.json">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="application-name" content="CryptoBot">
        <meta name="apple-mobile-web-app-title" content="CryptoBot">
        <meta name="msapplication-starturl" content="/">

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
                    <img class="logo" width="60" src="<?php echo Storage::url('cryptobot-logo-white-200px.png')?>"/>
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
                                <a href="#" data-open="notificationsModal"><i class="fa fa-bell-o badge-icon" aria-hidden="true"></i><span class="badge alert">{{ count(Auth::user()->Notifications) }}</span></a>
                                @endif
                                <button class="menu-icon" type="button" data-open="offCanvas"></button>
                            </div>
                        </div>

                        <div class="top-bar" id="example-menu">

                            <div class="top-bar-left show-for-medium">
                                <ul class="menu">
                                    <li><img class="logo" src="<?php echo asset('storage/cryptobot-logo-40px.png') ?>"/></li>
                                </ul>
                            </div>

                            <div class="top-bar-right">
                                <ul class="dropdown menu" data-dropdown-menu data-disable-hover="true" data-click-open="true">
                                    @if (Auth::guest())
                                        <li><a href="{{ route('login') }}">Login</i></a></li>
                                        <li><a href="{{ route('register') }}">Register</a></li>
                                    @else
                                        <li> <a href="{{ route('home') }}" title="Home">  <i class="fa fa-home" aria-hidden="true"></i> </a></li>
                                        <li> <a href="{{ route('dashboard') }}" title="Dashboard">  <i class="fa fa-tachometer" aria-hidden="true"></i></i></a></li>
                                        <li> <a href="{{ route('portfolio') }}" title="Portfolio">  <i class="fa fa-pie-chart" aria-hidden="true"></i></a></li>
                                        <li> <a href="{{ route('trades') }}" title="Trades">  <i class="fa fa-line-chart" aria-hidden="true"></i></a></li>
                                        <li><a href="{{ route('settings') }}" title="Settings" ><i class="fa fa-cog" aria-hidden="true"></i></a></li>
                                        <li  class="show-for-medium">
                                            <a href="#" data-open="notificationsModal" title="Alerts"><i class="fa fa-bell-o" aria-hidden="true"></i><span class="badge alert">{{ count(Auth::user()->Notifications) }}</span></a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}" title="Logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                                <i class="fa fa-sign-out" aria-hidden="true"></i>
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                        <li>
                                            <a href="#" title="Menu" ><i class="fa fa-th" aria-hidden="true" data-toggle="offCanvas"></i></a>
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
