<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran Parkir</title>
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
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        .code-box {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 8px;
            border: 2px solid #000;
            margin: 10px 0;
            background: #f0f0f0;
        }
        .details {
            margin: 10px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #ccc;
        }
        .detail-label {
            width: 50%;
            font-size: 11px;
        }
        .detail-value {
            width: 50%;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
        }
        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin: 15px 0 8px 0;
            padding-bottom: 3px;
            border-bottom: 2px solid #000;
        }
        .total-section {
            margin: 15px 0;
            padding: 10px;
            border: 2px solid #000;
            background: #f5f5f5;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total-label {
            font-size: 12px;
        }
        .total-value {
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 10px;
        }
        .paid-stamp {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 10px;
            border: 3px solid #000;
            margin: 15px 0;
            letter-spacing: 5px;
        }
        .separator {
            text-align: center;
            margin: 10px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>STRUK PEMBAYARAN</h1>
        <p>{{ config('app.name', 'PARKING SYSTEM') }}</p>
        <p>{{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <div class="code-box">
        {{ $transaction->unique_code }}
    </div>

    <div class="section-title">INFORMASI KENDARAAN</div>
    <div class="details">
        <div class="detail-row">
            <span class="detail-label">Plat Nomor:</span>
            <span class="detail-value">{{ $transaction->license_plate }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Jenis Kendaraan:</span>
            <span class="detail-value">{{ strtoupper($transaction->vehicle_type) }}</span>
        </div>
    </div>

    <div class="section-title">WAKTU PARKIR</div>
    <div class="details">
        <div class="detail-row">
            <span class="detail-label">Masuk:</span>
            <span class="detail-value">{{ $transaction->entry_time->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Keluar:</span>
            <span class="detail-value">{{ $transaction->exit_time->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Durasi:</span>
            <span class="detail-value">{{ $transaction->duration_hours }} JAM</span>
        </div>
    </div>

    <div class="section-title">PERHITUNGAN BIAYA</div>
    <div class="details">
        <div class="detail-row">
            <span class="detail-label">Tarif per Jam:</span>
            <span class="detail-value">Rp {{ number_format($transaction->hourly_rate ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Durasi ({{ $transaction->duration_hours }} jam):</span>
            <span class="detail-value">× {{ $transaction->duration_hours }}</span>
        </div>
        @if($transaction->overtime_duration > 0)
        <div class="detail-row">
            <span class="detail-label">Overtime ({{ $transaction->overtime_duration }} jam):</span>
            <span class="detail-value">Rp {{ number_format($transaction->overtime_fee ?? 0, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">TOTAL BAYAR:</span>
            <span class="total-value">Rp {{ number_format($transaction->parking_fee ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="paid-stamp">
        ✓ LUNAS
    </div>

    <div class="details">
        <div class="detail-row">
            <span class="detail-label">Petugas Masuk:</span>
            <span class="detail-value">{{ $transaction->entryUser->name ?? '-' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Petugas Keluar:</span>
            <span class="detail-value">{{ $transaction->exitUser->name ?? '-' }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Terima kasih atas kunjungan Anda</p>
        <p>Simpan struk ini sebagai bukti pembayaran</p>
        <p style="margin-top: 5px;">Dokumen ini sah tanpa tanda tangan</p>
    </div>
</body>
</html>
