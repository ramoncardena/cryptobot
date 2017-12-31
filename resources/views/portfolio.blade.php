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
                        @empty($portfolio)
                        <div>
                            <a class="button hollow" href="/settings"><span class="show-for-small-only"></span><i class="fa fa-cog" aria-hidden="true"></i> <span class="show-for-medium">Setup</span> </a>
                        </div>
                        @endempty
                        @isset($portfolio)
                        <div>
                            <button class="button hollow" data-open="new-asset-modal"><span class="show-for-small-only">+ </span><i class="fa fa-btc" aria-hidden="true"></i> <span class="show-for-medium">Add Asset</span> </button>
                            <button class="button hollow" data-open="new-origin-modal"><span class="show-for-small-only">+ </span><i class="fa fa-plug" aria-hidden="true"></i> <span class="show-for-medium">Add Origin</span> </button>
                        </div>
                        @endisset
                    </div>
                </div>
            </div>

            <div class="small-12 medium-8 cell portfolio form-container">

                    <div class="portfolio-area">
                       
                         <portfolio :portfolio="{{$portfolio}}"></portfolio>

                    </div>
            </div>
            <div class="small-12 medium-4 cell portfolio form-container">
                <canvas id="myChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

    <!-- MODAL: Add Origin-->
    <div class="reveal portfolio-modal" id="new-origin-modal" data-reveal>
        
        <add-origin :validation-errors="{{ $errors }}" :exchanges="{{$exchanges}}" :origin-types="{{$originTypes}}"></add-origin>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- MODAL: Add Asset -->
    <div class="reveal portfolio-modal" id="new-asset-modal" data-reveal>
        
        <add-asset :validation-errors="{{ $errors }}" :coins="{{$coins}}" :origins="{{$origins}}"></add-asset>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</section>
@endsection
