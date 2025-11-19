<?php
namespace App\Services;

use App\Models\ParkingTransaction;
use Illuminate\Support\Carbon;

class ParkingCodeGenerator
{
    public function generate(): string
    {
        $datePart = now()->format('Ymd');
        $countToday = ParkingTransaction::whereDate('entry_time', now()->toDateString())->count() + 1;
        $sequence = str_pad((string)$countToday, 4, '0', STR_PAD_LEFT);
        return 'PARK-' . $datePart . '-' . $sequence;
    }
}
