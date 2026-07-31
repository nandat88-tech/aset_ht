<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h2 { margin-bottom: 2px; }
        p.subtitle { color: #555; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h2>Laporan Peminjaman Handy Talky - Diskominfo</h2>
    <p class="subtitle">Dicetak pada: {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Departemen</th>
                <th>Lokasi Tujuan</th>
                <th>Keperluan</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Jumlah Unit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $trx)
                @php
                    $statusLabel = ['active' => 'Dipinjam', 'returned' => 'Selesai', 'returned_late' => 'Terlambat'][$trx->status] ?? $trx->status;
                @endphp
                <tr>
                    <td>{{ $trx->employee->name }}</td>
                    <td>{{ $trx->employee->department }}</td>
                    <td>{{ $trx->destinationLocation->name ?? '-' }}</td>
                    <td>{{ $trx->purpose ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->due_date)->format('d-m-Y') }}</td>
                    <td>{{ $trx->items->count() }}</td>
                    <td>{{ $statusLabel }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>