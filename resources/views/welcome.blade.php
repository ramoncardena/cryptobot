<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel {{ app()->version() }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <section id="home">
        <div class="grid-container flex-center position-ref full-height">
            <div class="grid-x grid-padding-x align-center">
                <div class="cell text-center">
                    <div class="title m-b-md">
                        CryptoBot
                        <p class="versioninfo">Your Platform For Crytocurrency Trading</p>
                    </div>
                    @if (Route::has('login'))

                        @if (Auth::check())
                        <a class="hollow button large" href="{{ url('/home') }}">Dashboard</a>
                        @else    
                        <div class="expanded button-group">
                          <a class="hollow button success"  href="{{ url('/login') }}">Login</a>
                          <a class="hollow button alert"  href="{{ url('/register') }}">Register</a>
                        </div>
                        @endif

                    @endif
                </div>
            </div>
        </div>
    </section>

  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
