<?php

use App\Models\BorrowTransaction;
use App\Models\Location;
use App\Models\ReturnTransaction;
use App\Models\ReturnItem;
use Livewire\Volt\Component;
use Livewire\Attributes\Locked;

new class extends Component
{
    #[Locked]
    public int $transactionId;

    public array $conditions = [];
    public array $conditionNotes = [];
    public string $notes = '';

    public function mount(int $transactionId): void
    {
        $this->transactionId = $transactionId;

        $transaction = BorrowTransaction::with('items')->findOrFail($transactionId);

        // Siapkan default kondisi "good" untuk tiap item
        foreach ($transaction->items as $item) {
            $this->conditions[$item->id] = 'good';
        }
    }

    public function with(): array
    {
        return [
            'transaction' => BorrowTransaction::with('employee', 'items.handyTalky', 'items.charger')
                ->findOrFail($this->transactionId),
        ];
    }

    public function submit(): void
    {
        $transaction = BorrowTransaction::with('items')->findOrFail($this->transactionId);

        $diskominfo = Location::firstOrCreate(['name' => 'DISKOMINFO']);

        $isLate = \Carbon\Carbon::parse($transaction->due_date)->isPast();

        $returnTransaction = ReturnTransaction::create([
            'borrow_transaction_id' => $transaction->id,
            'return_date' => now(),
            'notes' => $this->notes,
            'is_late' => $isLate,
        ]);

        foreach ($transaction->items as $item) {
    $condition = $this->conditions[$item->id] ?? 'good';
    $conditionNote = $this->conditionNotes[$item->id] ?? '';

    // Kalau rusak/perlu perbaikan, otomatis catat asal-usul peminjamannya untuk akuntabilitas
    if ($condition !== 'good') {
        $traceInfo = "Terjadi saat dipinjam oleh {$transaction->employee->name} ({$transaction->employee->department}), dipinjamkan ke lokasi: " . ($transaction->destinationLocation->name ?? '-') . ".";
        $conditionNote = trim($conditionNote . ' ' . $traceInfo);
    }

    ReturnItem::create([
        'return_transaction_id' => $returnTransaction->id,
        'handy_talky_id' => $item->handy_talky_id,
        'charger_id' => $item->charger_id,
        'condition' => $condition,
        'condition_note' => $conditionNote ?: null,
    ]);

            // Update status & lokasi aset sesuai kondisi
            // Lokasi selalu kembali ke DISKOMINFO (secara fisik unit sudah diterima kembali),
            // hanya status yang mengikuti kondisi (baik/rusak/perlu perbaikan)
                $newStatus = $condition === 'good' ? 'available' : $condition;

                if ($item->handy_talky_id) {
                $item->handyTalky->update([
                'condition' => $condition,
                'status' => $newStatus,
                'location_id' => $diskominfo->id,
            ]);
    }

if ($item->charger_id) {
    $item->charger->update([
        'condition' => $condition,
        'status' => $newStatus,
    ]);
}
        }

        $transaction->update([
            'status' => $isLate ? 'returned_late' : 'returned',
        ]);

        session()->flash('message', 'Pengembalian berhasil dicatat.');
        $this->redirect(route('returning.index'), navigate: true);
    }
}; ?>

<div class="max-w-2xl mx-auto">
    <div class="bg-card rounded-card shadow-card p-6">
        <h2 class="text-lg font-semibold mb-1">Proses Pengembalian</h2>
        <p class="text-sm text-text-secondary mb-4">
            Peminjam: <span class="font-medium text-text-primary">{{ $transaction->employee->name }}</span>
            ({{ $transaction->employee->department }})
        </p>

        <div class="space-y-3 mb-4">
            @foreach ($transaction->items as $item)
                <div class="border border-border rounded-control p-3">
                    <p class="text-sm font-medium mb-2">
                        @if ($item->handyTalky)
                            HT: {{ $item->handyTalky->serial_number }} ({{ $item->handyTalky->brand }})
                        @elseif ($item->charger)
                            Charger: {{ $item->charger->serial_number }}
                        @endif
                    </p>

                    <div class="flex gap-4 text-sm">
    <label class="flex items-center gap-1">
        <input type="radio" wire:model.live="conditions.{{ $item->id }}" value="good"> Baik
    </label>
    <label class="flex items-center gap-1">
        <input type="radio" wire:model.live="conditions.{{ $item->id }}" value="damaged"> Rusak
    </label>
    <label class="flex items-center gap-1">
        <input type="radio" wire:model.live="conditions.{{ $item->id }}" value="under_repair"> Perlu Perbaikan
    </label>
</div>

@if (($conditions[$item->id] ?? 'good') !== 'good')
    <div class="mt-2">
        <input type="text" wire:model="conditionNotes.{{ $item->id }}"
            placeholder="Detail kerusakan (contoh: layar retak, tidak menyala, dll)"
            class="block w-full rounded-control border-border text-xs">
    </div>
@endif
                </div>
            @endforeach
        </div>

        <div>
            <x-input-label for="notes" value="Catatan (opsional)" />
            <textarea id="notes" wire:model="notes" rows="3" class="mt-1 block w-full rounded-control border-border text-sm"></textarea>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('returning.index') }}" wire:navigate class="px-4 py-2 rounded-control text-sm border border-border hover:bg-gray-50">
                Batal
            </a>
            <button wire:click="submit" class="px-4 py-2 rounded-control text-sm bg-primary text-white hover:bg-primary-dark">
                Simpan Pengembalian
            </button>
        </div>
    </div>
</div>