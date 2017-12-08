@extends('layouts.app')

@section('content')

<section id="trades">

    <div class="grid-container fluid">
        <div class="grid-x grid-padding-x align-middle align-left">
            
            <!-- Header -->
            <div class="small-12 cell form-container section-title text-left">

                <div class="grid-x grid-padding-x">

                    <div class="small-4 cell text-left">
                        <div class="align-vertical">
                            <h1>Trades</h1>
                        </div>
                    </div>

                    <div class="small-8 cell text-right">
                        <div class="align-vertical">

                        </div>
                    </div>
                </div>
            </div>

            <!-- Trade Panel, Active Trades and History -->
            <div class="small-12 cell portfolio form-container">
                    <div class="trades-area">
                        <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                            <li class="accordion-item is-active" data-accordion-item>
                                <a href="#" class="accordion-title text-center">Trading Panel</a>
                                <div class="accordion-content" data-tab-content >
                                    <tradepanel></tradepanel>
                                </div>
                            </li>
                            <li class="accordion-item is-active" data-accordion-item>
                                <a href="#" class="accordion-title  text-center">Opened Trades</a>
                                <div class="accordion-content" data-tab-content >
                                    <tradelist2 type="opened" :trades="{{ $tradesOpened }}"></tradelist2>
                                </div>
                            </li>
                            <li class="accordion-item is-active" data-accordion-item>
                                <a href="#" class="accordion-title text-center">Conditional Trades</a>
                                <div class="accordion-content" data-tab-content >
                                    <tradelist2 type="waiting" :trades="{{ $tradesWaiting }}"></tradelist2>
                                </div>
                            </li>

                            <li class="accordion-item is-active" data-accordion-item>
                                <a href="#" class="accordion-title text-center">Trade History</a>
                                <div class="accordion-content" data-tab-content >
                                    <tradelist2 type="history" :trades="{{ $tradesHistory }}"></tradelist2>
                                </div>
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
    </section>
    @endsection
