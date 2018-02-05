<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewInvite extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    public $senderName;

    public $senderEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invitation, $senderName, $senderEmail)
    {
        $this->invitation = $invitation;
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       return $this->subject("Invitation to join CryptoBot from " . $this->senderName)->view('emails.invitations.newinvite');
    }
}
