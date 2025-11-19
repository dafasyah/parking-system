<?php
namespace App\Services;

use App\Models\ParkingRate;
use Illuminate\Support\Carbon;
use App\Models\ParkingTransaction;

class ParkingFeeCalculator
{
    public function calculate(ParkingTransaction $transaction): array
    {
        $entry = $transaction->entry_time;
        $exit = now();
        // ensure exit after entry
        if ($exit->lessThanOrEqualTo($entry)) {
            $exit = $entry->copy()->addHour();
        }
        $hours = (int) ceil(max(1, $entry->diffInMinutes($exit) / 60));
        $rate = ParkingRate::where('vehicle_type', $transaction->vehicle_type)->value('hourly_rate') ?? 0;
        $fee = $hours * $rate;
        return [
            'exit_time' => $exit,
            'duration_hours' => $hours,
            'parking_fee' => $fee,
        ];
    }
}
