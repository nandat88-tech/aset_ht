<?php

use App\Models\Employee;
use App\Models\HandyTalky;
use App\Models\Charger;
use App\Models\BorrowTransaction;
use App\Models\BorrowItem;
use Livewire\Volt\Component;

new class extends Component
{
    public int $step = 1;

    // Step 1
    public ?int $employee_id = null;
    public string $due_date = '';

    // Step 2
    public array $selectedHt = [];
    public array $selectedCharger = [];

    // Step 3
    public string $notes = '';

    public function with(): array
    {
        return [
            'employees' => Employee::orderBy('name')->get(),
            'availableHt' => HandyTalky::where('status', 'available')->orderBy('serial_number')->get(),
            'availableChargers' => Charger::where('status', 'available')->orderBy('serial_number')->get(),
        ];
    }

    public function goToStep2(): void
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $this->step = 2;
    }

    public function goToStep3(): void
    {
        if (empty($this->selectedHt) && empty($this->selectedCharger)) {
            $this->addError('selection', 'Pilih minimal 1 unit HT atau Charger.');
            return;
        }

        $this->step = 3;
    }

    public function backStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function submit(): void
    {
        $locationId = Employee::find($this->employee_id)?->location_id;

        $borrow = BorrowTransaction::create([
            'employee_id' => $this->employee_id,
            'borrow_date' => now(),
            'due_date' => $this->due_date,
            'notes' => $this->notes,
            'status' => 'active',
        ]);

        foreach ($this->selectedHt as $htId) {
            BorrowItem::create(['borrow_transaction_id' => $borrow->id, 'handy_talky_id' => $htId]);
            HandyTalky::where('id', $htId)->update(['status' => 'borrowed', 'location_id' => $locationId]);
        }

        foreach ($this->selectedCharger as $chargerId) {
            BorrowItem::create(['borrow_transaction_id' => $borrow->id, 'charger_id' => $chargerId]);
            Charger::where('id', $chargerId)->update(['status' => 'borrowed', 'location_id' => $locationId]);
        }

        session()->flash('message', 'Peminjaman berhasil dicatat.');
        $this->reset(['step', 'employee_id', 'due_date', 'selectedHt', 'selectedCharger', 'notes']);
        $this->step = 1;
    }
}; ?>

<div class="max-w-2xl mx-auto">
    @if (session('message'))
        <div class="mb-4 bg-green-50 text-success text-sm px-4 py-3 rounded-control">
            {{ session('message') }}
        </div>
    @endif

    <!-- Stepper -->
    <div class="flex items-center justify-center mb-8">
        @foreach (['Borrower Info', 'Select Assets', 'Terms & Upload'] as $index => $label)
            @php $num = $index + 1; @endphp
            <div class="flex items-center">
                <div class="h-8 w-8 rounded-full flex items-center justify-center text-sm font-semibold
                    {{ $step == $num ? 'bg-primary text-white' : ($step > $num ? 'bg-success text-white' : 'bg-gray-200 text-gray-500') }}">
                    {{ $step > $num ? '✓' : $num }}
                </div>
                <span class="ml-2 text-sm {{ $step == $num ? 'text-primary font-semibold' : 'text-text-secondary' }}">{{ $label }}</span>
                @if ($num < 3)
                    <div class="w-12 h-px bg-gray-300 mx-3"></div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="bg-card rounded-card shadow-card p-6">
        <!-- STEP 1 -->
        @if ($step === 1)
            <h2 class="text-lg font-semibold mb-4">1. Borrower Info</h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="employee_id" value="Peminjam" />
                    <select id="employee_id" wire:model="employee_id" class="mt-1 block w-full rounded-control border-border text-sm">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->department }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('employee_id')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="due_date" value="Rencana Tanggal Kembali" />
                    <input type="date" id="due_date" wire:model="due_date" class="mt-1 block w-full rounded-control border-border text-sm">
                    <x-input-error :messages="$errors->get('due_date')" class="mt-1" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button wire:click="goToStep2" class="px-4 py-2 rounded-control text-sm bg-primary text-white hover:bg-primary-dark">
                    Selanjutnya
                </button>
            </div>
        @endif

        <!-- STEP 2 -->
        @if ($step === 2)
            <h2 class="text-lg font-semibold mb-4">2. Select Assets</h2>

            <x-input-error :messages="$errors->get('selection')" class="mb-3" />

            <div class="mb-5">
                <p class="text-sm font-medium text-text-primary mb-2">Handy Talky Tersedia</p>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-border rounded-control p-3">
                    @forelse ($availableHt as $ht)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="selectedHt" value="{{ $ht->id }}">
                            {{ $ht->serial_number }} — {{ $ht->brand }} {{ $ht->model }}
                        </label>
                    @empty
                        <p class="text-sm text-text-secondary">Tidak ada HT tersedia saat ini.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-text-primary mb-2">Charger Tersedia (opsional)</p>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-border rounded-control p-3">
                    @forelse ($availableChargers as $charger)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="selectedCharger" value="{{ $charger->id }}">
                            {{ $charger->serial_number }}
                        </label>
                    @empty
                        <p class="text-sm text-text-secondary">Tidak ada Charger tersedia saat ini.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <button wire:click="backStep" class="px-4 py-2 rounded-control text-sm border border-border hover:bg-gray-50">Kembali</button>
                <button wire:click="goToStep3" class="px-4 py-2 rounded-control text-sm bg-primary text-white hover:bg-primary-dark">Selanjutnya</button>
            </div>
        @endif

        <!-- STEP 3 -->
        @if ($step === 3)
            <h2 class="text-lg font-semibold mb-4">3. Terms & Confirmation</h2>

            <div>
                <x-input-label for="notes" value="Catatan (opsional)" />
                <textarea id="notes" wire:model="notes" rows="3" class="mt-1 block w-full rounded-control border-border text-sm" placeholder="Contoh: Untuk kegiatan lapangan"></textarea>
            </div>

            <div class="mt-4 bg-gray-50 rounded-control p-4 text-sm">
                <p><span class="text-text-secondary">Jumlah HT dipilih:</span> {{ count($selectedHt) }}</p>
                <p><span class="text-text-secondary">Jumlah Charger dipilih:</span> {{ count($selectedCharger) }}</p>
            </div>

            <div class="mt-6 flex justify-between">
                <button wire:click="backStep" class="px-4 py-2 rounded-control text-sm border border-border hover:bg-gray-50">Kembali</button>
                <button wire:click="submit" class="px-4 py-2 rounded-control text-sm bg-primary text-white hover:bg-primary-dark">Simpan Peminjaman</button>
            </div>
        @endif
    </div>
</div>