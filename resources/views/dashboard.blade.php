@extends('layouts.app')

@section('content')

<section id="dashboard">

	<div class="grid-container fluid">

	    <div class="grid-x grid-padding-x align-middle align-center">

	        <div class="form-container section-title cell text-left">
	            
                    <div class="grid-x grid-padding-x align-center">

                        <div class="small-4 cell text-left">
                            <div class="align-vertical">
                                <h1>Dashboard</h1>
                            </div>
                        </div>

                        <div class="small-8 cell">
                            <div class="align-vertical">
                              
                            </div>
                        </div>
                    </div>
	        </div>

	        <div class="large-10 cell">

                <coin-card coin="ADA"> </coin-card> 

	        </div>

	   	</div>

	</div>

</section>

@endsection
