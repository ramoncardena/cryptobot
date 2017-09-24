@extends('layouts.app')

@section('content')

<section id="dashboard">
    <div class="grid-container ">

        <div class="grid-x">

            <div class="form-container cell">

            	<balance></balance>

                <div class="form-title text-center">
                    Dashboard

                    @foreach ($portfolio as $coin => $balance)
					    <p> {{ $coin }} : {{ $balance }} </p>

				
					@endforeach

 					@foreach ($values as $value)
					    <p> {{ $value }} BTC</p>
				
					@endforeach

					<p> Total: {{ $total }} BTC
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
