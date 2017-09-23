@extends('layouts.app')

@section('content')

<section id="dashboard">
    <div class="grid-container ">

        <div class="grid-x">

            <div class="form-container cell">

                <div class="form-title text-center">
                    Dashboard

                    @foreach ($balances as $balance)
                    	@if ($balance->Balance > 0)
					    <p> {{ $balance->Currency }}: {{ $balance->Balance }}: </p>

					    @endif
					@endforeach

                </div>

            </div>

        </div>

    </div>
</section>

@endsection
