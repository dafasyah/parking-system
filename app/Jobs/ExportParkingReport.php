<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Exports\ParkingTransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\ParkingTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportParkingReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $format, 
        public int $userId,
        public array $filters = []
    ) {}

    public function handle(): void
    {
        try {
            $fileName = 'parking-report-' . now()->format('YmdHis');
            $path = 'exports/' . $fileName . ($this->format === 'pdf' ? '.pdf' : '.xlsx');


            $query = ParkingTransaction::with(['entryUser', 'exitUser']);
            
            if (!empty($this->filters['from']) && !empty($this->filters['to'])) {
                $query->whereBetween('entry_time', [
                    Carbon::parse($this->filters['from'])->startOfDay(),
                    Carbon::parse($this->filters['to'])->endOfDay()
                ]);
            }
            
            if (!empty($this->filters['vehicle_type'])) {
                $vehicleTypes = is_array($this->filters['vehicle_type']) 
                    ? $this->filters['vehicle_type'] 
                    : [$this->filters['vehicle_type']];
                $query->whereIn('vehicle_type', $vehicleTypes);
            }
            
            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }
            
            $transactions = $query->latest()->limit(10000)->get();

            if ($this->format === 'pdf') {

                $html = view('reports.pdf', compact('transactions'))->render();
                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape')->output();
                Storage::disk('local')->put($path, $pdf);
            } else {

                Excel::store(new ParkingTransactionsExport($transactions), $path, 'local');
            }

            // Log success
            Log::info("Export report completed: {$path} for user {$this->userId}");
            
            activity()
                ->causedBy($this->userId)
                ->withProperties(['format' => $this->format, 'file' => $path, 'records' => $transactions->count()])
                ->log('Export laporan selesai');
                
        } catch (\Exception $e) {
            Log::error('Export report error: ' . $e->getMessage());
            throw $e; // Re-throw to mark job as failed
        }
    }
}
