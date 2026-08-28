<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .kop-surat { width: 100%; border-bottom: 3px solid #000; padding-bottom: 8px; margin-bottom: 16px; }
        .kop-table { width: 100%; border: none; }
        .kop-table td { border: none; padding: 0; vertical-align: middle; }
        .kop-logo { width: 70px; }
        .kop-text { text-align: center; }
        .kop-text h1 { font-size: 16px; margin: 0; text-transform: uppercase; }
        .kop-text h2 { font-size: 14px; margin: 2px 0; text-transform: uppercase; font-weight: bold; }
        .kop-text p { font-size: 10px; margin: 2px 0; }
        h2.judul-laporan { margin-bottom: 2px; text-align: center; margin-top: 12px; }
        p.subtitle { color: #555; margin-top: 0; margin-bottom: 16px; text-align: center; }
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table th, table.data-table td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        table.data-table th { background: #f3f4f6; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <img src="{{ public_path('images/logo-sawahlunto.png') }}" style="width: 60px;">
                </td>
                <td class="kop-text">
                    <h1>Pemerintah Kota Sawahlunto</h1>
                    <h2>Dinas Komunikasi dan Informatika</h2>
                    <p>Lantai II Blok A Pasar Sawahlunto Kode Pos 27411</p>
                    <p>Laman: http://sawahluntokota.go.id &nbsp;|&nbsp; Email: diskominfo@sawahluntokota.go.id</p>
                </td>
                <td class="kop-logo"></td>
            </tr>
        </table>
    </div>

    <h2 class="judul-laporan">LAPORAN PEMINJAMAN HANDY TALKY</h2>
    <p class="subtitle">Dicetak pada: {{ $generatedAt }}</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Departemen</th>
                <th>Lokasi Tujuan</th>
                <th>Keperluan</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Jumlah HT</th>
                <th>Jumlah Charger</th>
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
                    <td>{{ $trx->items->whereNotNull('handy_talky_id')->count() }}</td>
                    <td>{{ $trx->items->whereNotNull('charger_id')->count() }}</td>
                    <td>{{ $statusLabel }}</td>
                </tr>
            @empty
                <tr><td colspan="9">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
