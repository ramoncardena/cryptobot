@extends('layouts.app')

@section('content')

<section id="dashboard">

	<div class="grid-container fluid">
	    <div class="grid-x grid-margin-x align-middle align-center">
            <div class="small-12 cell text-center toolbar">
                <div class="expanded button-group">
                    <a class="hollow button"> Day </a>
                    <a class="hollow button"> Week </a>
                    <a class="hollow button"> Month </a>
                    <a class="hollow button"> Year </a>
                </div>
            </div>
	        <div class="small-12 cell">
                <div class="grid-container fluid">
                    <div class="grid-x grid-padding-x align-middle">
                        
                        <div class="small-12 medium-6 large-4 cell">
                            <coin-card2 coin="GNT" :compact="false"> </coin-card>
                        </div>

                        <div class="small-12 medium-6 large-4 cell">
                            <coin-card2 coin="NXT" :compact="false"> </coin-card>
                        </div>

                        <div class="small-12 medium-6 large-4 cell">
                            <coin-card2 coin="BTC" :compact="false"> </coin-card>
                        </div>
                        
                        <div class="small-12 medium-6 large-4 cell text-center">
                            <a href="#" data-open="new-ticker-modal"><i class="fa fa-plus add-coin" aria-hidden="true"></i>
                        </div>

                    </div>
                </div>
	        </div>
	   	</div>
	</div>
     <!-- MODAL: Add Ticker -->
    <div class="reveal portfolio-modal" id="new-ticker-modal" data-reveal>
        
        <add-ticker :validation-errors="{{ $errors }}" :coins="{{$coins}}"></add-ticker>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</section>

@endsection
