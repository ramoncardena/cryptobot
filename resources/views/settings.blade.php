@extends('layouts.app')

@section('content')

<section id="dashboard">
    <div class="grid-container ">

        <div class="grid-x">

            <div class="form-container cell">

            	<h1>Settings</h1>

            	@foreach ($settings as $key => $value)

					{{ $key }}: {{ $value}}           	
						
            	@endforeach
            </div>
        </div>
    </div>
</section>
@endsection