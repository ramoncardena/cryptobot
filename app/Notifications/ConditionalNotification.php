<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ConditionalNotification extends Notification
{
    use Queueable;

    public $trade;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($trade)
    {
        $this->trade = $trade;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'trade_id' => $this->trade->id,
            'exchange' => $this->trade->exchange,
            'pair' => $this->trade->pair,
            'price' => $this->trade->price,
            'stop' => $this->trade->stop,
            'profit' => $this->trade->profit,
            'message' => 'CONIDTIONAL TRADE OPENED at ' .  ucfirst($this->trade->exchange) .' for pair ' . $this->trade->pair .' with price ' . $this->trade->price . ' and condition ' . $this->trade->condition . ' ' . $this->trade->condition_price
        ];
    }
}
