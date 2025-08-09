<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    // Konstruktor prima podatke (asocijativni niz)
    public function __construct($details)
    {
        $this->details = $details;
    }

    // Izgradnja emaila
    public function build()
    {
        $mail = $this->from($this->details['from_email'], $this->details['from']) // Postavljamo pošiljaoca
                        ->subject($this->details['subject']) // Postavljamo subject
                        ->markdown($this->details['template'])
                        ->with([
                            'first_name' => $this->details['first_name'],
                            'last_name' => $this->details['last_name'],
                            'message' => $this->details['message'],
                        ]);

        // Proveravamo da li postoji ticket_id i ako postoji dodajemo ga u 'with' niz
        if (isset($this->details['ticket_id'])) {
            $mail->with(['ticket_id' => $this->details['ticket_id']]);
        }

        // Proveravamo da li postoji verificationUrl
        if (isset($this->details['verificationUrl'])) {
            $mail->with(['verificationUrl' => $this->details['verificationUrl']]);
        }

        // Proveravamo da li postoji resetUrl
        if (isset($this->details['resetUrl'])) {
            $mail->with(['resetUrl' => $this->details['resetUrl']]);
        }

        //Proveravamo da li postoji unreadMessages
        if(isset($this->details['unreadMessages'])){
            $mail->with([
                'first_name' => $this->details['first_name'],
                'last_name' => $this->details['last_name'],
                'message' => $this->details['message']
            ]);
        }

        //Proveravamo da li postoji unreadTickets
        if(isset($this->details['unreadTickets'])){
            $mail->with([
                'first_name' => $this->details['first_name'],
                'last_name' => $this->details['last_name'],
                'message' => $this->details['message'],
            ]);
        }

        return $mail->with($this->details);
    }
}
