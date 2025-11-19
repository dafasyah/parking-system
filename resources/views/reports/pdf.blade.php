<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Parkir</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        td {
            border: 1px solid #d1d5db;
            padding: 6px 5px;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRANSAKSI PARKIR</h1>
        <p>{{ config('app.name', 'Parking System') }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%">No</th>
                <th style="width: 15%">Kode Unik</th>
                <th style="width: 12%">Plat Nomor</th>
                <th style="width: 10%">Jenis</th>
                <th style="width: 15%">Waktu Masuk</th>
                <th style="width: 15%">Waktu Keluar</th>
                <th style="width: 8%">Durasi</th>
                <th class="text-right" style="width: 12%">Biaya</th>
                <th class="text-center" style="width: 10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $transaction->unique_code }}</td>
                    <td>{{ $transaction->license_plate }}</td>
                    <td>{{ ucfirst($transaction->vehicle_type) }}</td>
                    <td>{{ $transaction->entry_time->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->exit_time ? $transaction->exit_time->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">{{ $transaction->duration_hours ?? '-' }} jam</td>
                    <td class="text-right">Rp {{ number_format($transaction->parking_fee ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $transaction->status === 'parked' ? 'Parkir' : 'Selesai' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($transactions->isNotEmpty())
        <div class="summary">
            <strong>Ringkasan:</strong><br>
            Total Transaksi: {{ $transactions->count() }}<br>
            Total Pendapatan: Rp {{ number_format($transactions->where('status', 'completed')->sum('parking_fee'), 0, ',', '.') }}<br>
            Rata-rata: Rp {{ number_format($transactions->where('status', 'completed')->avg('parking_fee') ?? 0, 0, ',', '.') }}
        </div>
    @endif

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Dokumen ini dihasilkan secara otomatis oleh sistem.</p>
    </div>
</body>
</html>
