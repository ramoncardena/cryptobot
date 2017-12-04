@extends('layouts.app')

@section('content')

<section id="settings">
    <form method="POST" action="/settings">
        {{ csrf_field() }}
        <div class="grid-container fluid">

            <div class="grid-x grid-padding-x align-center">

                <div class="form-container section-title cell">

                    <div class="grid-x grid-padding-x">

                        <div class="small-6 cell text-left">
                            <div class="align-vertical">
                                <h1>Settings</h1>
                            </div>
                        </div>

                        <div class="small-6 cell text-right align-self-middle">
                            <div class="align-vertical">
                                <button type="submit" class="button" value="Update">Update</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="large-8 cell settings-area">
                    
                    <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Dashboard</a>
                            <div class="accordion-content" data-tab-content >

                                <div class="input-group">
                                    <span class="input-group-label">Fiat Currency</span>
                                    <select name="fiat" class="input-group-field"> 
                                        @if ($settings['fiat'] === 'usd')
                                            <option value="usd" selected="selected">USD</option>
                                        @else
                                             <option value="usd">USD</option>
                                        @endif
                                        @if ($settings['fiat'] === 'eur')
                                            <option value="eur" selected="selected">EUR</option>
                                        @else
                                            <option value="eur">EUR</option>
                                        @endif
                                    </select>
                                </div>

                            </div>
                        </li>
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Exchange APIs</a>
                            <div class="accordion-content" data-tab-content>
                                <div class="switch small">
                                    <input class="switch-input" id="yes-no" type="checkbox" name="exampleSwitch">
                                    <label class="switch-paddle" for="yes-no">
                                        <span class="switch-active" aria-hidden="true">Live</span>
                                        <span class="switch-inactive" aria-hidden="true">Test</span>
                                    </label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-label">Bittrex Key</span>
                                    <input class="input-group-field" type="text" name="bittrex_key" value="{{$decrypt_bittrex_key}}">
                                </div>

                                <div class="input-group">
                                    <span class="input-group-label">Bittrex Secret</span>
                                    <input class="input-group-field" type="text" name="bittrex_secret" value="{{$decrypt_bittrex_secret}}">
                                </div>

                            </div>
                        </li>
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Import</a>
                            <div class="accordion-content" data-tab-content>
                                
                                <label>BITTREX
                                  <input type="text" placeholder="{{$settings['bittrex_key']}}">
                                </label>
                            </div>
                        </li>
                    </ul>

    <!--                 @foreach ($settings as $key => $value)

                        <p> {{ $key }}: {{ $value}} </p>            
                            
                    @endforeach -->
                </div>

            </div>

        </div>
    </form>
</section>
@endsection