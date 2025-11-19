<?php
namespace App\Mail;

use App\Models\ParkingTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParkingReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ParkingTransaction $transaction)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Struk Parkir');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.parking_receipt', with: [
            'transaction' => $this->transaction,
        ]);
    }
}
