<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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

        return $mail;
    }
}
