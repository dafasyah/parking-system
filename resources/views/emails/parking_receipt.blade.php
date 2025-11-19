@component('mail::message')
# Struk Parkir

Kode: {{ $transaction->unique_code }}  
Plat: {{ $transaction->license_plate }}  
Jenis: {{ ucfirst($transaction->vehicle_type) }}  
Masuk: {{ $transaction->entry_time }}  
Keluar: {{ $transaction->exit_time }}  
Durasi: {{ $transaction->duration_hours }} jam  
Biaya: Rp {{ number_format($transaction->parking_fee,0,',','.') }}

Terima kasih telah menggunakan layanan parkir kami.

@endcomponent
