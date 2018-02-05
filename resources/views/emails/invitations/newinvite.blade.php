@extends('layouts.email')

@section('body')
<section id="email-newinvite">
	<div class="grid-container fluid">
	    <div class="grid-x grid-padding-x align-center">

	        <div class="cell small-12">
	        	<img src="{{ $message->embed('storage/cryptobot-logo-300px.png') }}" alt="CryptoBot">
			</div>
			<div class="cell small-12">
				<p class="h4">Your platform for cryptocurrency</p>
			</div>
		</div>
	</div>

	<div class="grid-container fluid">

	    <div class="grid-x grid-padding-x align-center">

	        <div class="cell small-10">
				<h1>You have been invited to join CryptoBot!</h1>

				<p class="lead"> Your friend <b>{{ $senderName}}</b> is sending you an invitation to join CryptoBot, an amazing cryptocurrency platform!</p>
				
				<div class="callout">
				  	<p class="invitation-label">Invitation Code</p> 
					<p class="invitation-code">{{ $invitation->code }}</p>
					<p class="invitation-label">Expiration Date</p> 
					<p class="invitation-expiration">{{ $invitation->expiration }}</p>
				</div>

				<p>You can go to our <a href="https://cryptobot.trading/register">registration page</a> and paste your invitation code there, or follow this link.</p>
				<p class="lead"><b>Thanks for joining us!</b></p>
				<p><i>CryptoBot Team</i></p>
			</div>
		</div>

	</div>
		
	<div class="grid-container fluid">
	    <div class="grid-x grid-padding-x align-center">

	        <div class="cell small-12">
				<!-- <columns large="6">
					<h5>Connect With Us:</h5>
					<button class="facebook expand" href="http://zurb.com">Facebook</button>
					<button class="twitter expand" href="http://zurb.com">Twitter</button>
					<button class="google expand" href="http://zurb.com">Google+</button>
				</columns> -->
			</div>
			<div class="cell small-12">
				<h5>Contact Info:</h5>
				<p>Email: <a href="mailto:admin@cryptobot.trading">admin@cryptobot.trading</a></p>
			</div>
		</div>
	</div>
</section>
@endsection