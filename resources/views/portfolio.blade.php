@extends('layouts.app')

@section('content')

<section id="portfolio">

    @if( Session::has('status-text') )
        <div class="callout {{ Session::get('status-class') }} alerts-callout" data-closable>
            <div class="alerts-callout-text">{{ Session::get('status-text') }}</div>
            <button class="close-button" aria-label="Dismiss" type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="off-canvas-wrapper">
        <div class="off-canvas position-right" id="portfolio-offCanvas" data-off-canvas data-auto-focus="false">

            <div class="menu-title text-center">
                <img class="logo" width="60" src="<?php echo Storage::url('cryptobot-logo-white-200px.png')?>"/>
                CRYPTOBOT
            </div>
            <!-- Menu -->
            <ul class="vertical menu text-center">
                <li class="h4">Origins</li>
                <li><a  href="#" data-open="new-origin-modal"><i class="fa fa-plus" aria-hidden="true"></i> <span class="nowrap">Add Origin</span> </a></li>
                <li><a href="#" data-open="edit-origin-modal"><i class="fa fa-pencil" aria-hidden="true"></i> <span class="nowrap">Edit Origin</span> </a></li>
                <li><a href="#" data-open="delete-origin-modal"><i class="fa fa-pencil" aria-hidden="true"></i> <span class="nowrap">Delete Origin</span> </a></li>
                <li class="h4">Assets</li>
                <li><a href="#" data-open="new-asset-modal"><i class="fa fa-plus" aria-hidden="true"></i> <span class="nowrap">Add Asset</span> </a></li>
                <li><a href="#" data-open="edit-asset-modal"><i class="fa fa-pencil" aria-hidden="true"></i> <span class="nowrap">Edit Asset</span> </a></li>
                <li><a href="#" data-open="delete-asset-modal"><i class="fa fa-times" aria-hidden="true"></i> <span class="nowrap">Delete Asset</span> </a></li>
                <li class="h4">Transactions</li>
                <li><a href="#" data-open="add-transaction-modal"><i class="fa fa-plus" aria-hidden="true"></i> <span class="nowrap">Add Transaction</span> </a></li>
                <li><a href="#" data-open="edit-ransaction-modal"><i class="fa fa-pencil" aria-hidden="true"></i> <span class="nowrap">Edit Transaction</span> </a></li>
                <li><a href="#" data-open="delete-transaction-modal"><i class="fa fa-pencil" aria-hidden="true"></i> <span class="nowrap">Delete Transaction</span> </a></li>
            </ul>

         </div>
        <div class="off-canvas-content" data-off-canvas-content>
            <div class="grid-container fluid">
                
                <div class="grid-x grid-padding-x align-middle align-left">
                    
                    <!-- Header -->
                    <div class="small-12 cell form-container section-title text-left">

                        <div class="grid-x grid-padding-x align-middle">

                            <div class="small-4 cell text-left">
                                <div>
                                    <h1>Portfolio</h1>
                                </div>
                            </div>

                            <div class="small-8 cell text-right">
                                @empty($portfolio)
                                <div>
                                    <a class="button hollow" href="/settings"><i class="fa fa-cog" aria-hidden="true"></i> <span class="show-for-medium">Setup</span> </a>
                                </div>
                                @endempty
                                @isset($portfolio)
                                <div>
                                    <a class="button hollow" href="#" data-toggle="portfolio-offCanvas"><i class="fa fa-wrench" aria-hidden="true"></i><span class="show-for-medium"> Tools</span></a>
                                </div>
                                @endisset
                            </div>
                        </div>
                    </div>

                    <div class="small-12 cell portfolio form-container">

                            <div class="portfolio-area">
                               
                                 <portfolio :portfolio="{{$portfolio}}"></portfolio>

                            </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>

    <!-- MODAL: Add Origin-->
    <div class="reveal portfolio-modal" id="new-origin-modal" data-reveal>
        
        <add-origin :validation-errors="{{ $errors }}" :exchanges="{{$exchanges}}" :origin-types="{{$originTypes}}"></add-origin>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <!-- MODAL: Edit Origin-->
    <div class="reveal portfolio-modal" id="edit-origin-modal" data-reveal>
        
        <edit-origin :validation-errors="{{ $errors }}" :exchanges="{{$exchanges}}" :origin-types="{{$originTypes}}" :origins="{{$origins}}" ></edit-origin>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <!-- MODAL: Delete Origin-->
    <div class="reveal portfolio-modal" id="delete-origin-modal" data-reveal>
        
        <delete-origin :validation-errors="{{ $errors }}" :exchanges="{{$exchanges}}" :origin-types="{{$originTypes}}" :origins="{{$origins}}" ></delete-origin>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- MODAL: Add Asset -->
    <div class="reveal portfolio-modal" id="new-asset-modal" data-reveal>
        
        <add-asset :validation-errors="{{ $errors }}" :coins="{{$coins}}" :origins="{{$origins}}"  :exchanges="{{$exchanges}}"></add-asset>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <!-- MODAL: Edit Asset -->
    <div class="reveal portfolio-modal" id="edit-asset-modal" data-reveal>
         
        <edit-asset :validation-errors="{{ $errors }}" :portfolio="{{$portfolio}}" :origins="{{$origins}}" :exchanges="{{$exchanges}}"></edit-asset>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <!-- MODAL: Delete Asset -->
    <div class="reveal portfolio-modal" id="delete-asset-modal" data-reveal>
         
        <delete-asset :validation-errors="{{ $errors }}" :portfolio="{{$portfolio}}" :origins="{{$origins}}" :exchanges="{{$exchanges}}"></delete-asset>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <!-- MODAL: Add Transaction -->
    <div class="reveal portfolio-modal" id="add-transaction-modal" data-reveal>
        
        <add-transaction :validation-errors="{{ $errors }}" :portfolio="{{$portfolio}}" :origins="{{$origins}}" :exchanges="{{$exchanges}}"></add-transaction>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <!-- MODAL: Edit Transactions-->
    <div class="reveal portfolio-modal" id="edit-transaction-modal" data-reveal>
        
        <add-transaction :validation-errors="{{ $errors }}" :portfolio="{{$portfolio}}" :origins="{{$origins}}" :exchanges="{{$exchanges}}"></add-transaction>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <!-- MODAL: Delete Transaction -->
    <div class="reveal portfolio-modal" id="delete-transaction-modal" data-reveal>
        
        <delete-transaction :validation-errors="{{ $errors }}" :portfolio="{{$portfolio}}" :origins="{{$origins}}" :exchanges="{{$exchanges}}"></delete-transaction>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    
</section>
@endsection
