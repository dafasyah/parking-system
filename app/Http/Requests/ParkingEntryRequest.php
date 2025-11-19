<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ParkingTransaction;

class ParkingEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // handled via middleware/roles
    }

    public function rules(): array
    {
        return [
            // Regex ringan; pesan error utamanya di-handle manual di withValidator
            'license_plate' => ['required','string','max:20'],
            'vehicle_type' => ['required','in:motor,mobil'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('license_plate')) {
            $this->merge([
                'license_plate' => strtoupper($this->input('license_plate')),
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function($v){
            $plate = $this->license_plate;

            if ($plate && !preg_match('/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/', $plate)) {
                $v->errors()->forget('license_plate');
                $v->errors()->add('license_plate', 'Format plat nomor tidak valid. Contoh format yang benar: B 1234 ABC');
            }

            if ($plate && ParkingTransaction::where('license_plate', $plate)->where('status','parked')->exists()) {
                $v->errors()->add('license_plate', 'Kendaraan dengan nomor polisi ini masih terparkir.');
            }
        });
    }
}
