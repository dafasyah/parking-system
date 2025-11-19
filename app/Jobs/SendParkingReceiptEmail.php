<?php
namespace App\Jobs;

use App\Models\ParkingTransaction;
use App\Mail\ParkingReceiptMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendParkingReceiptEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public ParkingTransaction $transaction)
    {
    }

    public function handle(): void
    {
        $user = $this->transaction->exitUser ?: $this->transaction->entryUser;
        if ($user && $user->email) {
            Mail::to($user->email)->send(new ParkingReceiptMail($this->transaction));
        }
    }
}
