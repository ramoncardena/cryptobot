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
    <link href="node_modules/open-iconic/font/css/open-iconic-foundation.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="off-canvas-wrapper">
        <div class="off-canvas position-right" id="offCanvas" data-off-canvas data-auto-focus="false">

            <!-- Menu -->

            <div class="menu-title text-center">
                <img class="logo" width="60" src="<?php echo asset('storage/cryptobot-logo-white-200px.png') ?>"/>
                CryptoBot
            </div>
            <ul class="vertical menu text-center">
                <li><a href="/home">Dashboard</a></li>
                <li><a href="#">Watchlist</a></li>
                <li><a href="#">Orders</a></li>
                <li><a href="#">Trades</a></li>
                <li><a href="#">Documentation</a></li>
                <li><a href="#">Support</a></li>
                <li><a href="/settings">Settings</a></li>
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
                  <button class="menu-icon" type="button" data-toggle="example-menu"></button>
                  <div class="title-bar-title">Menu</div>
              </div>

              <div class="top-bar" id="example-menu">

                <div class="top-bar-left">
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
                        <li> <a href="{{ route('home') }}">  {{ Auth::user()->email }} </a></li>
                        
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                       

                        
                        <li><a href="/settings"><i class="fa fa-cog" aria-hidden="true"></i></a></li>

                        <li>
                            <a href="#"><i class="fa fa-th" aria-hidden="true" data-toggle="offCanvas"></i></a>
                      
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

</div>
</div>
</body>
</html>
