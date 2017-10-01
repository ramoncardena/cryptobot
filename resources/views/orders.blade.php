@extends('layouts.app')

@section('content')

<section id="orders">

	<div class="grid-container fluid">

	    <div class="grid-x grid-padding-x align-middle align-center">

	        <div class="form-container section-title cell text-left">
	            
                    <div class="grid-x grid-padding-x">

                        <div class="small-4 cell text-left">
                            <div class="align-vertical">
                                <h1>Orders</h1>
                            </div>
                        </div>

                        <div class="small-8 cell text-right">
                            <div class="align-vertical">
                                
                            </div>
                        </div>
                    </div>
	        </div>

	        <div class="large-8 cell text-center portfolio form-container">
                <div class="orders-area">
                    <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Open Orders</a>
                            <div class="accordion-content" data-tab-content >
                            @foreach ($bittrexOpenOrders as $bittrexOpenOrder)
                                <p>Pair: {{ $bittrexOpenOrder->Exchange }}</p>
                                <p>Type: {{ $bittrexOpenOrder->OrderType }}</p>
                                <p>Quantity: {{ $bittrexOpenOrder->Quantity }}</p>
                                <p>Limit: {{ $bittrexOpenOrder->Limit }}</p>
                                <hr>
                            @endforeach
                            </div>
                        </li>
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Order History</a>
                            <div class="accordion-content" data-tab-content >
                                @foreach ($bittrexOrderHistory as $bittrexOrder)
                                    <p>Pair: {{ $bittrexOrder->Exchange }}</p>
                                    <p>Type: {{ $bittrexOrder->OrderType }}</p>
                                    <p>Quantity: {{ $bittrexOrder->Quantity }}</p>
                                    <p>Limit: {{ $bittrexOrder->Limit }}</p>
                                    <hr>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
	        </div>
	   	</div>
	</div>
</section>

@endsection
