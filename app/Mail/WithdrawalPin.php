<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalPin extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user , $pin)
    {
        $this->user = $user;
        $this->pin = $pin;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.withdrawal-pin')
                        ->subject('Withdrawal Pin')
                        ->with('user', $this->user)
                        ->with('pin', $this->pin);
    }

//    /**
//     * Get the message envelope.
//     *
//     * @return \Illuminate\Mail\Mailables\Envelope
//     */
//    public function envelope()
//    {
//        return new Envelope(
//            subject: 'Withdrawal Pin',
//        );
//    }
//
//    /**
//     * Get the message content definition.
//     *
//     * @return \Illuminate\Mail\Mailables\Content
//     */
//    public function content()
//    {
//        return new Content(
//            markdown: 'emails.withdrawal-pin',
//        );
//    }
//
//    /**
//     * Get the attachments for the message.
//     *
//     * @return array
//     */
//    public function attachments()
//    {
//        return [];
//    }
}
