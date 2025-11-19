<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ParkingTransaction;

class ParkingExitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unique_code' => ['required','exists:parking_transactions,unique_code'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($v){
            $transaction = ParkingTransaction::where('unique_code',$this->unique_code)->first();
            if ($transaction && $transaction->status === 'completed') {
                $v->errors()->add('unique_code','Transaksi sudah selesai.');
            }
        });
    }
}
