@extends('layouts.app')

@section('content')

<section id="invitations">
    @if( Session::has('status-text') )
        <div class="callout {{ Session::get('status-class') }} alerts-callout" data-closable>
            <div class="alerts-callout-text">{{ Session::get('status-text') }}</div>
            <button class="close-button" aria-label="Dismiss" type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
	<div class="grid-container fluid">

	    <div class="grid-x grid-padding-x align-middle">

	        <div class="cell form-container section-title text-left">
	            
                    <div class="grid-x grid-padding-x align-middle">

                        <div class="small-4 cell text-left">
                            <div class="align-vertical">
                                <h1>Invitations</h1>
                            </div>
                        </div>

                        <div class="small-8 cell text-right">
                            <div class="align-vertical">
                               
                            </div>
                        </div>
                    </div>
	        </div>

	        <div class="cell text-center content-area">
                <p class="invitations-title">Here you can manage your invitations for your friends to join CryptoBot!</p>

                <p class="invitations-subtitle">Fill the form below with the email of your friend:</p>  

                <invite-panel :validation-errors="{{ $errors }}" invitations=""></invite-panel>
                @isset($invitation_code)
                <div class="invitation-instructions">The invitation has been sent to your friend to unlock registration. You can keep a copy for your records.</div>
                <div class="invitation-label">Invitation Code</div>
                <div class="invitation-code">{{ $invitation_code }}</div>
                @endisset
                @isset($invitation_email)
                <div class="invitation-label">Invited Email</div>
                <div class="invitation-email">{{ $invitation_email }}</div>
                @endisset
                @isset($invitation_expiration)
                <div class="invitation-label">Exipration</div>
                <div class="invitation-expiration">{{ $invitation_expiration }}</div>
                @endisset
                
                @isset($invitation_code)
                <p class="invitations-thanks">Thanks for making CryptoBot bigger!</p>
                @endisset
	        </div>

	   	</div>

	</div>

</section>

@endsection
