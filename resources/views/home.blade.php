@extends('layouts.app')

@section('content')

<section id="dashboard">

	<div class="grid-container fluid">

	    <div class="grid-x grid-padding-x align-middle align-center">

	        <div class="form-container section-title cell text-left">
	            
                    <div class="grid-x grid-padding-x align-middle">

                        <div class="small-4 cell text-left">
                            <div class="align-vertical">
                                <h1>Dashboard</h1>
                            </div>
                        </div>

                        <div class="small-8 cell text-right">
                            <div class="align-vertical">
                                @if ($fiat === 'eur')
                                    <div class="value"> {{$totals['BTC']}}BTC / {{ $totals['EUR'] }}€ </div>
                                @elseif ($fiat === 'usd')
                                    <div class="value"> {{$totals['BTC']}}BTC / ${{ $totals['USD'] }} </div>
                                @endif
                            </div>
                        </div>
                    </div>
	        </div>

	        <div class="large-10 cell text-center portfolio form-container">

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
                        fiat="{{ $fiat }}">
                    </balance>
                 @endforeach            

	        </div>

	   	</div>

	</div>

</section>

@endsection
