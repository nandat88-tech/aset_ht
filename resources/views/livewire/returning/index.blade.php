<?php

use App\Models\BorrowTransaction;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function with(): array
    {
        return [
            'transactions' => BorrowTransaction::with('employee', 'items')
                ->where('status', 'active')
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div>
    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Peminjam</th>
                    <th class="text-left px-4 py-3">Instansi</th>
                    <th class="text-left px-4 py-3">Tanggal Pinjam</th>
                    <th class="text-left px-4 py-3">Jatuh Tempo</th>
                    <th class="text-left px-4 py-3">Jumlah Unit</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($transactions as $trx)
                    @php $isLate = $trx->due_date && \Carbon\Carbon::parse($trx->due_date)->isPast(); @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $trx->employee->name }}</td>
                        <td class="px-4 py-3">{{ $trx->employee->department }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $trx->due_date ? \Carbon\Carbon::parse($trx->due_date)->format('d M Y') : 'Tanpa batas (Tetap)' }}</td>
                        <td class="px-4 py-3">{{ $trx->items->count() }} unit</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $isLate ? 'badge-danger' : 'badge-info' }}">
                                {{ $isLate ? 'Terlambat' : 'Dipinjam' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('returning.process', $trx->id) }}" wire:navigate class="text-primary hover:underline">
                                Proses Pengembalian
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-text-secondary">
                            Tidak ada peminjaman aktif saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
</div>