@extends('layouts.app')

@section('content')

<section id="dashboard">
    <div class="grid-container fluid">
        <div class="grid-x grid-padding-x">

            <div class="form-container cell section-title">

                <div class="text-left">
               
                        <h1>Settings</h1>

                </div>
            </div>

            <div class="cell">
                <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                    <li class="accordion-item" data-accordion-item>
                        <a href="#" class="accordion-title">Dashboard</a>
                        <div class="accordion-content" data-tab-content >
                            <p>Panel 1. Lorem ipsum dolor</p>
                            <a href="#">Nowhere to Go</a>
                        </div>
                    </li>
                    <li class="accordion-item" data-accordion-item>
                        <a href="#" class="accordion-title">Exchange APIs</a>
                        <div class="accordion-content" data-tab-content>
                            <h2>Bittrex</h2>
                            <label>BITTREX Key
                              <input type="text" placeholder="{{$settings['bittrex_key']}}">
                            </label>
                            <label>BITTREX Secret
                              <input type="text" placeholder="{{$settings['bittrex_secret']}}">
                            </label>
                        </div>
                    </li>
                    <li class="accordion-item" data-accordion-item>
                        <a href="#" class="accordion-title">Personal</a>
                        <div class="accordion-content" data-tab-content>
                            Type your name!
                            <input type="text"></input>
                        </div>
                    </li>
                </ul>

<!--                 @foreach ($settings as $key => $value)

                    <p> {{ $key }}: {{ $value}} </p>            
                        
                @endforeach -->
            </div>

        </div>

    </div>

</section>
@endsection