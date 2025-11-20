<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket Parkir Masuk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #000;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        .qr-section {
            text-align: center;
            margin: 15px 0;
            padding: 10px 0;
            background: #fff;
        }
        .qr-section img {
            width: 150px;
            height: 150px;
            border: 2px solid #000;
            padding: 5px;
            background: #fff;
        }
        .code-box {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 10px;
            border: 2px solid #000;
            margin: 10px 0;
            background: #f0f0f0;
        }
        .details {
            margin: 15px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }
        .detail-label {
            font-weight: bold;
            width: 45%;
        }
        .detail-value {
            width: 55%;
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 10px;
        }
        .notice {
            text-align: center;
            font-size: 11px;
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #000;
            background: #ffffcc;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TIKET PARKIR</h1>
        <p>{{ config('app.name', 'PARKING SYSTEM') }}</p>
        <p>{{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <div class="qr-section">
        @if($transaction->qr_code_path && file_exists(storage_path('app/public/' . $transaction->qr_code_path)))
            @if(str_ends_with($transaction->qr_code_path, '.svg'))
                {!! file_get_contents(storage_path('app/public/' . $transaction->qr_code_path)) !!}
            @else
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/' . $transaction->qr_code_path))) }}" alt="QR Code">
            @endif
        @endif
    </div>

    <div class="code-box">
        {{ $transaction->unique_code }}
    </div>

    <div class="details">
        <div class="detail-row">
            <span class="detail-label">PLAT NOMOR:</span>
            <span class="detail-value">{{ $transaction->license_plate }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">JENIS KENDARAAN:</span>
            <span class="detail-value">{{ strtoupper($transaction->vehicle_type) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">WAKTU MASUK:</span>
            <span class="detail-value">{{ $transaction->entry_time->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">PETUGAS:</span>
            <span class="detail-value">{{ $transaction->entryUser->name ?? 'SYSTEM' }}</span>
        </div>
    </div>

    <div class="notice">
        SIMPAN TIKET INI<br>
        TUNJUKKAN SAAT KELUAR
    </div>

    <div class="footer">
        <p>Terima kasih telah menggunakan layanan kami</p>
        <p>Harap parkir dengan rapi dan aman</p>
        <p style="margin-top: 5px;">Dokumen ini sah tanpa tanda tangan</p>
    </div>
</body>
</html>
