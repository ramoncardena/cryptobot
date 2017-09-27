@extends('layouts.app')

@section('content')

<section id="dashboard">

	<div class="grid-container fluid">

	    <div class="grid-x grid-padding-x">

	        <div class="form-container cell section-title">

	            <div class="text-left">
	           
	                	<h1>Dashboard</h1>

	            </div>


	        </div>

	        <div class="form-container cell text-center">
	
				<balance type="header"></balance>
                @foreach ($coins as $coin)
					
                	<balance 
                    	type="item" 
                    	coin="{{ $coin['Name'] }}" 
                    	logo="{{ $coin['LogoUrl'] }}" 
                    	balance="{{ $coin['Balance'] }}"  
                    	price="{{ $coin['Price'] }}" 
                    	valueBTC="{{ $coin['BTC-Value'] }}" 
                    	valueEUR="{{ $coin['EUR-Value'] }}" 
                    	valueUSD="{{ $coin['USD-Value'] }}" 
                    	gain="">
                	</balance>
			
				@endforeach

	        </div>

	   	</div>

	</div>

</section>

@endsection
