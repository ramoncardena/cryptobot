<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CryptoBot') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">

    <nav class="navigation">
        <div class="title-bar" data-responsive-toggle="example-menu" data-hide-for="medium">
              <button class="menu-icon" type="button" data-toggle="example-menu"></button>
              <div class="title-bar-title">Menu</div>
        </div>

        <div class="top-bar" id="example-menu">
            <div class="top-bar-left">
                <ul class="menu">
                    <li><img class="logo" src="<?php echo asset('storage/cryptobot-logo-40px.png') ?>"/></li>
                    @auth
                    <li> <a href="{{ route('home') }}">  {{ Auth::user()->name }} </a></li>
                    @endauth
                </ul>
            </div>
            <div class="top-bar-right">
                <ul class="menu">
                    @if (Auth::guest())
                        <li><a href="{{ route('login') }}">Login</i></a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @else
                        
                        <li><a href="#">Watchlist</a></li>
                        <li><a href="#">Orders</a></li>
                        <li><a href="#">Trades</a></li>
                        <li><a href="#"><i class="fa fa-cog" aria-hidden="true"></i></a></li>
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                       
                    @endif
                </ul>
            </div>
        </div>
    </nav>

        @yield('content')

    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        $(document).foundation();
    </script>
</body>
</html>
