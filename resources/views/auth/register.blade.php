@extends('layouts.app')

@section('content')

<section id="login">
    <div class="grid-container flex-center position-ref full-height">
        <div class="grid-x align-center">

             <div class="form-container cell text-center">

                <div class="login-icon">
                    <i class="fa fa-user-plus" aria-hidden="true"></i> 
                </div>
                <div class="form-title">
                    CryptoBot
                </div>

                <form class="register-form" method="POST" action="{{ route('register') }}">

                    {{ csrf_field() }}

                    <div class="name">
                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" aria-describedby="nameHelpText" placeholder="Name" required autofocus>

                        @if ($errors->has('name'))
                        <span class="help-text" id="nameHelpText">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="email">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" aria-describedby="emailHelpText" placeholder="E-Mail Address" required>

                        @if ($errors->has('email'))
                        <span class="help-text" id="emailHelpText">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="password">
                        <input id="password" type="password" name="password" aria-describedby="passwordHelpText"  placeholder="Password" required>

                        @if ($errors->has('password'))
                        <span class="help-text" id="passwordHelpText">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>

                    <div class="password-confirm">
                        <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm Password" required>
                    </div>

                    <div class="register_button">
                        <button type="submit" class="hollow button large">
                            Register
                        </button>
                    </div>


                </form>

            </div>

        </div>

    </div>
</section>

@endsection
