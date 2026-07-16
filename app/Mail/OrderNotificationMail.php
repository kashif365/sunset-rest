<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'NEW ORDER '.$this->order->order_number.' — pickup '
                .$this->order->pickup_date->format('D M j').' '.substr($this->order->pickup_time, 0, 5),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-notification');
    }
}
