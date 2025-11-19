<?php
namespace App\Http\Controllers;

use App\Http\Requests\ParkingEntryRequest;
use App\Models\ParkingTransaction;
use App\Services\ParkingCodeGenerator;
use App\Services\QRCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParkingEntryController extends Controller
{
    public function create(): View
    {
        return view('parking.entry');
    }

    public function store(ParkingEntryRequest $request, ParkingCodeGenerator $codeGen, QRCodeService $qr): RedirectResponse
    {
        $data = $request->validated();
        $code = $codeGen->generate();
        $transaction = null;
        
        DB::transaction(function() use ($data, $code, $qr, &$transaction) {
            $transaction = ParkingTransaction::create([
                'unique_code' => $code,
                'license_plate' => $data['license_plate'],
                'vehicle_type' => $data['vehicle_type'],
                'entry_time' => now(),
                'processed_by_entry' => Auth::id(),
                'status' => 'parked',
            ]);
            $path = $qr->generateAndStore($code);
            $transaction->update(['qr_code_path' => $path]);
        });
        
        Cache::forget('dashboard:stats');
        
        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($transaction)
            ->withProperties([
                'license_plate' => $transaction->license_plate,
                'vehicle_type' => $transaction->vehicle_type,
                'unique_code' => $transaction->unique_code,
            ])
            ->log('Vehicle entry recorded');
        
        return redirect()->route('parking.entry.success', $transaction->id);
    }
    
    public function success($id): View
    {
        $transaction = ParkingTransaction::findOrFail($id);
        return view('parking.entry-success', compact('transaction'));
    }
    
    public function downloadQR($id)
    {
        $transaction = ParkingTransaction::findOrFail($id);
        
        if (!$transaction->qr_code_path) {
            abort(404, 'QR Code tidak ditemukan');
        }
        
        $filename = $transaction->unique_code . '.png';
        return response()->download(
            storage_path('app/public/' . $transaction->qr_code_path),
            $filename
        );
    }
    
    public function printTicket($id)
    {
        $transaction = ParkingTransaction::with('entryUser')->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('parking.entry-ticket-pdf', compact('transaction'))
            ->setPaper([0, 0, 226.77, 566.93], 'portrait'); // 80mm x 200mm thermal paper
        
        return $pdf->stream('tiket-masuk-' . $transaction->unique_code . '.pdf');
    }
}
