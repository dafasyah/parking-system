<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ParkingRate;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParkingRateController extends Controller
{
    /**
     * Display a listing of parking rates.
     */
    public function index(): View
    {
        $rates = ParkingRate::all();
        
        // If no rates exist, create default rates
        if ($rates->isEmpty()) {
            ParkingRate::create(['vehicle_type' => 'motor', 'hourly_rate' => 2000]);
            ParkingRate::create(['vehicle_type' => 'mobil', 'hourly_rate' => 5000]);
            $rates = ParkingRate::all();
        }
        
        return view('settings.parking-rates.index', compact('rates'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $rate = ParkingRate::findOrFail($id);
        return view('settings.parking-rates.edit', compact('rate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $rate = ParkingRate::findOrFail($id);
        
        $validated = $request->validate([
            'hourly_rate' => ['required', 'numeric', 'min:1000', 'max:100000'],
        ], [
            'hourly_rate.required' => 'Tarif per jam wajib diisi',
            'hourly_rate.numeric' => 'Tarif harus berupa angka',
            'hourly_rate.min' => 'Tarif minimal Rp 1.000',
            'hourly_rate.max' => 'Tarif maksimal Rp 100.000',
        ]);
        
        $oldRate = $rate->hourly_rate;
        $rate->update($validated);
        
        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($rate)
            ->withProperties([
                'old_rate' => $oldRate,
                'new_rate' => $validated['hourly_rate'],
                'vehicle_type' => $rate->vehicle_type,
            ])
            ->log("Updated parking rate for {$rate->vehicle_type} from Rp " . number_format($oldRate, 0, ',', '.') . " to Rp " . number_format($validated['hourly_rate'], 0, ',', '.'));
        
        return redirect()
            ->route('settings.parking-rates.index')
            ->with('success', "Tarif parkir {$rate->vehicle_type} berhasil diperbarui menjadi Rp " . number_format($validated['hourly_rate'], 0, ',', '.'));
    }
    
    /**
     * Other methods not needed for rate management
     */
    public function create()
    {
        abort(404);
    }
    
    public function store(Request $request)
    {
        abort(404);
    }
    
    public function show(string $id)
    {
        abort(404);
    }
    
    public function destroy(string $id)
    {
        abort(404);
    }
}
