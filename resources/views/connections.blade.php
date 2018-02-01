@extends('layouts.app')

@section('content')

<section id="connections">
        @if( Session::has('status-text') )
            <div class="callout {{ Session::get('status-class') }} alerts-callout" data-closable>
                <div class="alerts-callout-text">{{ Session::get('status-text') }}</div>
                <button class="close-button" aria-label="Dismiss" type="button" data-close>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="grid-container fluid">

            <div class="grid-x grid-padding-x align-center">

                <div class="form-container section-title cell">

                    <div class="grid-x grid-padding-x align-middle">

                        <div class="small-6 cell text-left">
                            <div class="align-vertical">
                                <h1>Exchange APIs</h1>
                            </div>
                        </div>

                        <div class="small-6 cell text-right align-self-middle">
                            <div class="align-vertical">
                                
                            </div>
                        </div>

                    </div>
                </div>

                <div class="large-8 cell settings-area">
                    <connections :validation-errors="{{ $errors }}" :sources="{{$connections}}" :exchanges="{{$exchanges}}"> </connections>
                </div>
            </div>
        </div>
</section>
@endsection