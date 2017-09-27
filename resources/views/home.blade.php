@extends('layouts.app')

@section('content')

<section id="dashboard">
    <div class="grid-container ">

        <div class="grid-x">

            <div class="form-container cell">

                <div class="form-title text-center">
                	<div class="section-title">
                    	
                    	<h1>Dashboard</h1>

                	</div>
					
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

    </div>
</section>

@endsection
