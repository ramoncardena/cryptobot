@extends('layouts.app')

@section('content')

<section id="dashboard">

	<div class="grid-container fluid">
	    <div class="grid-x grid-padding-x align-middle align-center">
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
                            <coin-card coin="ADA" :compact="false"> </coin-card>
                        </div>

                        <div class="small-12 medium-6 large-4 cell">
                            <coin-card coin="NXT" :compact="false"> </coin-card>
                        </div>

                        <div class="small-12 medium-6 large-4 cell">
                            <coin-card coin="BTC" :compact="true"> </coin-card>
                        </div>
                        
                        <div class="small-12 medium-6 large-4 cell text-center">
                            <a href="#"><i class="fa fa-plus add-coin" aria-hidden="true"></i>
                        </div>

                    </div>
                </div>
	        </div>
	   	</div>
	</div>

</section>

@endsection
