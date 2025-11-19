<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ExportParkingReport;
use App\Models\ParkingTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $query = ParkingTransaction::with(['entryUser', 'exitUser']);
            
            if ($request->filled('from') && $request->filled('to')) {
                $query->whereBetween('entry_time', [
                    Carbon::parse($request->from)->startOfDay(),
                    Carbon::parse($request->to)->endOfDay()
                ]);
            }
            
            if ($request->filled('vehicle_type')) {
                $vehicleTypes = is_array($request->vehicle_type) ? $request->vehicle_type : [$request->vehicle_type];
                $query->whereIn('vehicle_type', $vehicleTypes);
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $sort = $request->input('sort', 'created_at');
            $dir = $request->input('dir', 'desc');
            
            $summaryQuery = clone $query;
            $summary = [
                'count' => $summaryQuery->count(),
                'total' => $summaryQuery->where('status', 'completed')->sum('parking_fee'),
                'average' => $summaryQuery->where('status', 'completed')->avg('parking_fee') ?? 0,
            ];
            
            $transactions = $query->orderBy($sort, $dir)
                ->paginate(20)
                ->appends($request->only(['from', 'to', 'vehicle_type', 'status', 'sort', 'dir']));
            
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => $request->ip(), 'filters' => $request->only(['from', 'to', 'vehicle_type', 'status'])])
                ->log('Mengakses halaman laporan');
            
            return view('reports.index', compact('transactions', 'summary'));
        } catch (\Exception $e) {
            Log::error('Report index error: ' . $e->getMessage());
            return view('reports.index')->with('error', 'Gagal memuat laporan');
        }
    }

    public function export(Request $request)
    {
        try {
            $validated = $request->validate([
                'format' => ['required', 'in:excel,pdf'],
                'from' => ['nullable', 'date'],
                'to' => ['nullable', 'date', 'after_or_equal:from'],
                'vehicle_type' => ['nullable'],
                'status' => ['nullable', 'in:parked,completed'],
            ]);
            
            $format = $request->input('format', 'excel');
            $filters = $request->only(['from', 'to', 'vehicle_type', 'status']);
            

            $query = ParkingTransaction::with(['entryUser', 'exitUser']);
            
            if (!empty($filters['from']) && !empty($filters['to'])) {
                $query->whereBetween('entry_time', [
                    Carbon::parse($filters['from'])->startOfDay(),
                    Carbon::parse($filters['to'])->endOfDay()
                ]);
            }
            
            if (!empty($filters['vehicle_type'])) {
                $vehicleTypes = is_array($filters['vehicle_type']) 
                    ? $filters['vehicle_type'] 
                    : [$filters['vehicle_type']];
                $query->whereIn('vehicle_type', $vehicleTypes);
            }
            
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            $transactions = $query->latest()->limit(5000)->get();
            
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => $request->ip(), 'format' => $format, 'filters' => $filters, 'records' => $transactions->count()])
                ->log('Export laporan format: ' . $format);
            
            $fileName = 'laporan-parkir-' . now()->format('YmdHis');
            
            if ($format === 'pdf') {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('transactions'))
                    ->setPaper('a4', 'landscape');
                return $pdf->download($fileName . '.pdf');
            } else {
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\ParkingTransactionsExport($transactions),
                    $fileName . '.xlsx'
                );
            }
        } catch (\Exception $e) {
            Log::error('Report export error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses export: ' . $e->getMessage());
        }
    }
}
