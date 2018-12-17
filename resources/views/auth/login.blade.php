@extends('layouts.app')

@section('content')
<section id="login">
    <div class="grid-container flex-center position-ref full-height">
        <div class="grid-x align-center">

            <div class="form-container cell text-center">

                <div class="login-icon">
                    <i class="fa fa-user-o" aria-hidden="true"></i> 
                </div>
                <div class="form-title">
                    CryptoBot
                </div>
                

                <form class="login-form" method="POST" action="{{ route('login') }}">

                    {{ csrf_field() }}

                    <div class="email">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" aria-describedby="emailHelpText" placeholder="E-Mail Address" required autofocus>

                        @if ($errors->has('email'))
                        <span class="help-text" id="emailHelpText">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="password">
                        <input id="password" type="password" name="password" aria-describedby="passwordHelpText" placeholder="Password" required>

                        @if ($errors->has('password'))
                        <span class="help-text" id="passwordHelpText">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                        </label>
                    </div>

                    <div class="button-plus-link">
                        <button type="submit" class="hollow button large">
                            Login
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('password.request') }}">
                            &nbsp;
                            Forgot Your Password?
                        </a>
                    </div>
                </form>

            </div>

        </div>
    </div>
</section>

@endsection
