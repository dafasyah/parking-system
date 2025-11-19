<?php
namespace App\Http\Controllers;

use App\Http\Requests\ParkingExitRequest;
use App\Models\ParkingTransaction;
use App\Services\ParkingFeeCalculator;
use App\Jobs\SendParkingReceiptEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParkingExitController extends Controller
{
    public function create(): View
    {
        return view('parking.exit-unified');
    }

    public function store(ParkingExitRequest $request, ParkingFeeCalculator $calc): RedirectResponse
    {
        $transaction = ParkingTransaction::where('unique_code', $request->unique_code)
            ->where('status', 'parked')
            ->firstOrFail();

        DB::transaction(function() use ($transaction, $calc) {
            $feeData = $calc->calculate($transaction);
            $transaction->fill($feeData);
            $transaction->status = 'completed';
            $transaction->processed_by_exit = Auth::id();
            $transaction->save();
        });

        Cache::forget('dashboard:stats');
        SendParkingReceiptEmail::dispatch($transaction)->onQueue('high');
        
        return redirect()->route('parking.exit.success', $transaction->id);
    }

    public function success(int $id): View
    {
        $transaction = ParkingTransaction::with(['entryUser', 'exitUser'])
            ->findOrFail($id);

        if ($transaction->status !== 'completed') {
            abort(403, 'Transaksi belum selesai');
        }

        activity()
            ->causedBy(Auth::user())
            ->performedOn($transaction)
            ->withProperties(['ip' => request()->ip()])
            ->log('Melihat struk pembayaran parkir');

        return view('parking.exit-success', compact('transaction'));
    }
    
        public function check(Request $request)
    {
        $code = $request->input('code');
        
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $transaction = ParkingTransaction::where('unique_code', $code)
            ->where('status', 'parked')
            ->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan atau sudah selesai'], 404);
        }

        return response()->json([
            'unique_code' => $transaction->unique_code,
            'license_plate' => $transaction->license_plate,
            'plate_number' => $transaction->license_plate, // Alias for compatibility
            'vehicle_type' => $transaction->vehicle_type,
            'entry_time' => $transaction->entry_time?->toDateTimeString(),
        ]);
    }
    
    public function printReceipt($id)
    {
        $transaction = ParkingTransaction::with(['entryUser', 'exitUser'])->findOrFail($id);
        
        if ($transaction->status !== 'completed') {
            abort(403, 'Transaksi belum selesai');
        }
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('parking.exit-receipt-pdf', compact('transaction'))
            ->setPaper([0, 0, 226.77, 566.93], 'portrait'); // 80mm x 200mm thermal paper
        
        return $pdf->stream('struk-keluar-' . $transaction->unique_code . '.pdf');
    }
}
