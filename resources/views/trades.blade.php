@extends('layouts.app')

@section('content')

<section id="trades">

    <div class="grid-container fluid">
        <div class="grid-x grid-padding-x align-middle align-left">

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
            <!-- Trade Panel -->
            <div class="small-12 cell ">
                <tradepanel></tradepanel>   
            </div>
            
            <div class="small-12 cell text-center portfolio form-container">
                    <div class="trades-area">
                        <ul class="accordion" data-accordion data-multi-expand="true" data-allow-all-closed="true">
                            <li class="accordion-item" data-accordion-item>
                                <a href="#" class="accordion-title">Open Trades</a>
                                <div class="accordion-content" data-tab-content >

                                </div>
                            </li>
                            <li class="accordion-item" data-accordion-item>
                                <a href="#" class="accordion-title">Trades History</a>
                                <div class="accordion-content" data-tab-content >

                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
        </div>
    </section>

    @endsection
