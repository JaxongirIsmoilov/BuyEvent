<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InvoicePaid extends Notification
{
    use Queueable;

    protected $notification;
    protected $channel = null;

    public function __construct($ntfchannel, $msg)
    {
        $this->channel = $ntfchannel;

        $this->notification = $msg;
    }

    public function via($notifiable)
    {
        if (!$this->channel) {
            throw new \Exception('Sending a message failed. No channel provided.');
        }
        return $this->channel;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->error()
            ->line('Your purchase ' . $this->notification)
            ->line('Thank you!');

    }

    public function toNexmo($notifiable)
    {
        try {
            return (new NexmoMessage)
                ->content('This content about your purchase ' . $this->notification);
        } catch (\Exception $e) {
            Log::error('SMS failed...');
        }

    }

    public function toArray($notifiable)
    {
        return [
            'data' => $this->notification

        ];
    }

}
