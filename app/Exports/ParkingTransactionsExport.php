<?php
namespace App\Exports;

use App\Models\ParkingTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ParkingTransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $transactions;
    
    public function __construct($transactions = null)
    {
        $this->transactions = $transactions;
    }
    
    public function collection()
    {
        if ($this->transactions) {
            return $this->transactions;
        }
        
        return ParkingTransaction::select([
            'unique_code','license_plate','vehicle_type','entry_time','exit_time','duration_hours','parking_fee','status'
        ])->latest()->limit(1000)->get();
    }
    
    public function map($transaction): array
    {
        return [
            $transaction->unique_code,
            $transaction->license_plate,
            ucfirst($transaction->vehicle_type),
            $transaction->entry_time ? $transaction->entry_time->format('d/m/Y H:i') : '-',
            $transaction->exit_time ? $transaction->exit_time->format('d/m/Y H:i') : '-',
            $transaction->duration_hours ?? 0,
            'Rp ' . number_format($transaction->parking_fee ?? 0, 0, ',', '.'),
            $transaction->status === 'parked' ? 'Parkir' : 'Selesai',
        ];
    }

    public function headings(): array
    {
        return ['Kode','Plat','Jenis','Masuk','Keluar','Durasi (Jam)','Biaya','Status'];
    }
}
