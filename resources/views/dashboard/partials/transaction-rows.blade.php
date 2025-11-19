@foreach($collection as $transaction)
    <tr class="border-b border-gray-100 hover:bg-gray-50 text-xs">
        <td class="px-4 py-2 font-mono text-[11px] text-gray-800">{{ $transaction->unique_code }}</td>
        <td class="px-4 py-2">{{ $transaction->license_plate }}</td>
        <td class="px-4 py-2 capitalize">{{ $transaction->vehicle_type }}</td>
        <td class="px-4 py-2 text-gray-600">{{ optional($transaction->entry_time)->format('d M Y H:i') }}</td>
        <td class="px-4 py-2 text-gray-600">{{ optional($transaction->exit_time)->format('d M Y H:i') ?? '-' }}</td>
        <td class="px-4 py-2 text-center">{{ $transaction->duration_hours ?? '-' }}</td>
        <td class="px-4 py-2 text-right">Rp {{ number_format($transaction->parking_fee ?? 0, 0, ',', '.') }}</td>
        <td class="px-4 py-2">
            @if($transaction->status === 'parked')
                <span class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-0.5 text-[11px] font-medium text-yellow-800 border border-yellow-200">Parkir</span>
            @else
                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-[11px] font-medium text-green-800 border border-green-200">Selesai</span>
            @endif
        </td>
    </tr>
@endforeach
@if($collection->isEmpty())
    <tr>
        <td colspan="8" class="px-4 py-6 text-center text-xs text-gray-400">Belum ada transaksi.</td>
    </tr>
@endif
