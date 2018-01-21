@extends('layouts.app')

@section('content')

<section id="settings">
    <form method="POST" action="/settings">
        {{ csrf_field() }}
        <div class="grid-container fluid">

            <div class="grid-x grid-padding-x align-center">

                <div class="form-container section-title cell">

                    <div class="grid-x grid-padding-x align-middle">

                        <div class="small-6 cell text-left">
                            <div class="align-vertical">
                                <h1>Settings</h1>
                            </div>
                        </div>

                        <div class="small-6 cell text-right align-self-middle">
                            <div class="align-vertical">
                                <button type="submit" class="button hollow" value="Update">Update</button>
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
                            <a href="#" class="accordion-title">Portfolio</a>
                            <div class="accordion-content" data-tab-content >
                                @empty ($portfolio) 
                                <span class="switch">
                                    <input class="switch-input" id="initialize-portfolio" type="checkbox" name="initialize_portfolio">
                                    <label class="switch-paddle" for="initialize-portfolio">
                                        <span class="show-for-sr">Initialize Portfolio</span>
                                        <span class="switch-active" aria-hidden="true">Yes</span>
                                        <span class="switch-inactive" aria-hidden="true">No</span>
                                    </label>
                                </span>
                                @endempty
                                @isset($portfolio)
                                <div class="input-group">
                                    <span class="input-group-label">Name</span>
                                    @if($portfolio->name === "") 
                                        <input name="portfolio_name" class="input-group-field" type="text"> 
                                    @else
                                        <input name="portfolio_name" class="input-group-field" type="text" value="{{$portfolio->name}}"> 
                                    @endif
                                </div>
                                <div class="input-group">
                                    <span class="input-group-label" >Counter Value Currency</span>
                                    <select name="portfolio_countervalue" class="input-group-field"> 
                                       
                                        @if ( $portfolio->counter_value === '')
                                            <option disabled value="" selected="selected">Select...</option>)
                                        @endif 
                                        @if ($portfolio->counter_value === 'usd')
                                            <option value="usd" selected="selected">USD</option>
                                        @else
                                             <option value="usd">USD</option>
                                        @endif
                                        @if ($portfolio->counter_value === 'eur')
                                            <option value="eur" selected="selected">EUR</option>
                                        @else
                                             <option value="eur">EUR</option>
                                        @endif

                                    </select>
                                </div>
                                @endisset

                            </div>
                        </li>
                        
                    </ul>

                </div>

            </div>

        </div>
    </form>
</section>
@endsection