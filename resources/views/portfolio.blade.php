@extends('layouts.app')

@section('content')

<section id="portfolio">

    @if( Session::has('status-text') )
        <div class="callout {{ Session::get('status-class') }} alerts-callout" data-closable>
            <div class="alerts-callout-text">{{ Session::get('status-text') }}</div>
            <button class="close-button" aria-label="Dismiss" type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="grid-container fluid">
        
        <div class="grid-x grid-padding-x align-middle align-left">
            
            <!-- Header -->
            <div class="small-12 cell form-container section-title text-left">

                <div class="grid-x grid-padding-x align-middle">

                    <div class="small-4 cell text-left">
                        <div>
                            <h1>Portfolio</h1>
                        </div>
                    </div>

                    <div class="small-8 cell text-right">
                        <div>
                            <button class="button hollow"><span class="show-for-small-only">+ </span><i class="fa fa-btc" aria-hidden="true"></i> <span class="show-for-medium">Add Asset</span> </button>
                            <button class="button hollow"><span class="show-for-small-only">+ </span><i class="fa fa-plug" aria-hidden="true"></i> <span class="show-for-medium">Add Origin</span> </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trade Panel, Active Trades and History -->
            <div class="small-12 cell portfolio form-container">
                    <div class="trades-area">
                        <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                            <li class="accordion-item is-active" data-accordion-item>
                                <a href="#" class="accordion-title text-center">Assets</a>
                                
                                <div class="accordion-content" data-tab-content >

                                </div>
                            </li>
                            <li class="accordion-item is-active" data-accordion-item>
                                <a href="#" class="accordion-title  text-center">By Origin</a>
                                <div class="accordion-content" data-tab-content >
                                   
                                </div>
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
    </section>
    @endsection
