<?php

use App\Models\BorrowTransaction;
use App\Models\Location;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $month = '';
    public string $year = '';
    public string $locationFilter = '';

    public function mount(): void
    {
        $this->year = now()->format('Y');
    }

    public function with(): array
    {
        $query = BorrowTransaction::with('employee', 'destinationLocation', 'items', 'returnTransaction');

        if ($this->year) {
            $query->whereYear('borrow_date', $this->year);
        }

        if ($this->month) {
            $query->whereMonth('borrow_date', $this->month);
        }

        if ($this->locationFilter) {
            $query->where('destination_location_id', $this->locationFilter);
        }

        $transactions = (clone $query)->latest('borrow_date')->paginate(15);

        return [
            'transactions' => $transactions,
            'locations' => Location::orderBy('name')->get(),
            'totalTransaksi' => (clone $query)->count(),
            'totalTerlambat' => (clone $query)->where('status', 'returned_late')->count(),
            'totalAktif' => (clone $query)->where('status', 'active')->count(),
        ];
    }

    public function resetFilter(): void
    {
        $this->reset(['month', 'locationFilter']);
        $this->year = now()->format('Y');
    }
}; ?>

<div>
    <!-- Filter -->
    <div class="bg-card rounded-card shadow-card p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <x-input-label for="year" value="Tahun" />
                <select id="year" wire:model.live="year" class="mt-1 block w-full rounded-control border-border text-sm">
                    @for ($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <x-input-label for="month" value="Bulan" />
                <select id="month" wire:model.live="month" class="mt-1 block w-full rounded-control border-border text-sm">
                    <option value="">Semua Bulan</option>
                    @foreach (['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $label)
                        <option value="{{ $num }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="locationFilter" value="Lokasi Tujuan" />
                <select id="locationFilter" wire:model.live="locationFilter" class="mt-1 block w-full rounded-control border-border text-sm">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button wire:click="resetFilter" class="px-4 py-2 rounded-control text-sm border border-border hover:bg-gray-50 w-full">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="bg-card rounded-card shadow-card p-4">
            <p class="text-text-secondary text-xs font-medium mb-1">Total Transaksi</p>
            <p class="text-2xl font-bold text-text-primary">{{ $totalTransaksi }}</p>
        </div>
        <div class="bg-card rounded-card shadow-card p-4">
            <p class="text-text-secondary text-xs font-medium mb-1">Sedang Dipinjam</p>
            <p class="text-2xl font-bold text-text-primary">{{ $totalAktif }}</p>
        </div>
        <div class="bg-card rounded-card shadow-card p-4">
            <p class="text-text-secondary text-xs font-medium mb-1">Pengembalian Terlambat</p>
            <p class="text-2xl font-bold text-danger">{{ $totalTerlambat }}</p>
        </div>
    </div>

    <!-- Tombol Export -->
    <div class="flex justify-end gap-3 mb-4">
        <a href="{{ route('reports.export-pdf', ['year' => $year, 'month' => $month, 'location' => $locationFilter]) }}"
            target="_blank"
            class="px-4 py-2 rounded-control text-sm bg-danger text-white hover:opacity-90">
            Export PDF
        </a>
        <a href="{{ route('reports.export-excel', ['year' => $year, 'month' => $month, 'location' => $locationFilter]) }}"
            class="px-4 py-2 rounded-control text-sm bg-success text-white hover:opacity-90">
            Export Excel
        </a>
    </div>

    <!-- Tabel -->
    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Peminjam</th>
                    <th class="text-left px-4 py-3">Departemen</th>
                    <th class="text-left px-4 py-3">Lokasi Tujuan</th>
                    <th class="text-left px-4 py-3">Keperluan</th>
                    <th class="text-left px-4 py-3">Tgl Pinjam</th>
                    <th class="text-left px-4 py-3">Jatuh Tempo</th>
                    <th class="text-left px-4 py-3">Jumlah Unit</th>
                    <th class="text-left px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($transactions as $trx)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $trx->employee->name }}</td>
                        <td class="px-4 py-3">{{ $trx->employee->department }}</td>
                        <td class="px-4 py-3">{{ $trx->destinationLocation->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $trx->purpose ?? '-' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($trx->due_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $trx->items->count() }} unit</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $trx->status === 'active' ? 'badge-info' : ($trx->status === 'returned_late' ? 'badge-danger' : 'badge-success') }}">
                                {{ ['active' => 'Dipinjam', 'returned' => 'Selesai', 'returned_late' => 'Terlambat'][$trx->status] ?? $trx->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-text-secondary">
                            Tidak ada data untuk filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
</div>