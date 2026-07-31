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
            'transactions' => BorrowTransaction::with('employee', 'destinationLocation', 'items')
                ->where('status', 'active')
                ->whereDate('due_date', '<', now())
                ->orderBy('due_date')
                ->paginate(10),
        ];
    }

    public function sendReminder(int $id): void
    {
        BorrowTransaction::where('id', $id)->update([
            'last_reminder_sent_at' => now(),
        ]);

        session()->flash('message', 'Reminder tercatat berhasil dikirim.');
    }
}; ?>

<div>
    @if (session('message'))
        <div class="mb-4 bg-green-50 text-success text-sm px-4 py-3 rounded-control">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Peminjam</th>
                    <th class="text-left px-4 py-3">Departemen</th>
                    <th class="text-left px-4 py-3">Lokasi Tujuan</th>
                    <th class="text-left px-4 py-3">Tanggal Pinjam</th>
                    <th class="text-left px-4 py-3">Jatuh Tempo</th>
                    <th class="text-left px-4 py-3">Terlambat</th>
                    <th class="text-left px-4 py-3">Reminder Terakhir</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($transactions as $trx)
                    @php $daysLate = (int) \Carbon\Carbon::parse($trx->due_date)->diffInDays(now()); @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $trx->employee->name }}</td>
                        <td class="px-4 py-3">{{ $trx->employee->department }}</td>
                        <td class="px-4 py-3">{{ $trx->destinationLocation->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($trx->due_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="badge badge-danger">{{ $daysLate }} hari</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-text-secondary">
                            {{ $trx->last_reminder_sent_at ? $trx->last_reminder_sent_at->diffForHumans() : 'Belum pernah' }}
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="sendReminder({{ $trx->id }})" class="text-primary hover:underline">
                                Kirim Reminder
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-text-secondary">
                            Tidak ada peminjaman yang terlambat saat ini. 🎉
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
</div>