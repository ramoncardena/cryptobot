@extends('layouts.app')

@section('content')

<section id="dashboard">

	<div class="grid-container fluid">
	    <div class="grid-x grid-padding-x align-middle align-center">
            <div class="small-12 cell toolbar">

            </div>
	        <div class="small-12 cell">
                <div class="grid-container fluid">
                    <div class="grid-x grid-padding-x align-center">
                        
                        <div class="small-12 medium-6 large-4 cell">
                            <coin-card coin="ADA"> </coin-card>
                        </div>

                        <div class="small-12 medium-6 large-4 cell">
                            <coin-card coin="NXT"> </coin-card>
                        </div>

                        <div class="small-12 medium-6 large-4 cell">
                            <coin-card coin="BTC"> </coin-card>
                        </div>

                    </div>
                </div>
	        </div>
	   	</div>
	</div>

</section>

@endsection
