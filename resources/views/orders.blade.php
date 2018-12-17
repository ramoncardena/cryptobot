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

	        <div class="cell text-center portfolio form-container">
                <div class="orders-area">
                    <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                        <li class="accordion-item is-active" data-accordion-item>
                            <a href="#" class="accordion-title">Open Orders</a>
                            <div class="accordion-content" data-tab-content >
                            <?php $header = true ?>
                            @foreach ($bittrexOpenOrders as $bittrexOpenOrder)
                                <order 
                                    header = "{{ $header }}"
                                    opened = "{{ true }}"
                                    pair = "{{ $bittrexOpenOrder->Exchange }}"
                                    type = "{{ $bittrexOpenOrder->OrderType }}"
                                    quantity = "{{ $bittrexOpenOrder->Quantity }}"
                                    remaining = "{{ $bittrexOpenOrder->QuantityRemaining }}"
                                    limit = "{{ $bittrexOpenOrder->Limit }}"
                                >
                                </order>
                                <?php $header = false ?>
                            @endforeach
                            </div>
                        </li>
                        <li class="accordion-item" data-accordion-item>
                            <a href="#" class="accordion-title">Order History</a>
                            <div class="accordion-content" data-tab-content >
                                <?php $header = true ?>
                                @foreach ($bittrexOrderHistory as $bittrexOrder)
                                    <order 
                                        header = "{{ $header }}"
                                        pair = "{{ $bittrexOrder->Exchange }}"
                                        type = "{{ $bittrexOrder->OrderType }}"
                                        quantity = "{{ $bittrexOrder->Quantity }}"
                                        remaining = "{{ $bittrexOrder->QuantityRemaining }}"
                                        limit = "{{ $bittrexOrder->Limit }}"
                                        price = "{{ $bittrexOrder->Price }}"
                                        priceunit = "{{ $bittrexOrder->PricePerUnit }}"
                                        commission = "{{ $bittrexOrder->Commission }}"
                                        closed = "{{ $bittrexOrder->Closed }}"
                                    >
                                    </order>
                                    <?php $header = false ?>
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
