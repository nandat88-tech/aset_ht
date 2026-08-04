<?php

use App\Models\Employee;
use App\Models\HandyTalky;
use App\Models\Charger;
use App\Models\BorrowTransaction;
use App\Models\BorrowItem;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1
    public ?int $employee_id = null;
    public string $borrow_date = '';
    public string $due_date = '';
    public ?int $destination_location_id = null;
    public string $purpose = '';
    public string $loan_type = 'sementara';

    // Step 2
    public array $selectedHt = [];
    public array $selectedCharger = [];

    // Step 3
    public string $notes = '';
    public $document = null;

    public function mount(): void
    {
        $this->borrow_date = now()->format('Y-m-d');
    }

    public function with(): array
{
    return [
        'employees' => Employee::orderBy('name')->get(),
        'availableHt' => HandyTalky::where('status', 'available')->orderBy('serial_number')->get(),
        'availableChargers' => Charger::where('status', 'available')->orderBy('serial_number')->get(),
        'locations' => \App\Models\Location::orderBy('name')->get(),
    ];
}

    public function goToStep2(): void
{
    $this->validate([
        'employee_id' => 'required|exists:employees,id',
        'borrow_date' => 'required|date',
        'destination_location_id' => 'required|exists:locations,id',
        'due_date' => $this->loan_type === 'sementara' ? 'required|date|after_or_equal:borrow_date' : 'nullable',
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
    $this->validate([
        'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);
    
    $documentPath = null;
    if ($this->document) {
        $documentPath = $this->document->store('borrow-documents', 'public');
    }

    $borrow = BorrowTransaction::create([
        'employee_id' => $this->employee_id,
        'destination_location_id' => $this->destination_location_id,
        'loan_type' => $this->loan_type,
        'borrow_date' => $this->borrow_date,
        'due_date' => $this->loan_type === 'sementara' ? $this->due_date : null,
        'notes' => $this->notes,
        'purpose' => $this->purpose,
        'document_url' => $documentPath,
        'status' => 'active',
    ]);

    foreach ($this->selectedHt as $htId) {
        BorrowItem::create(['borrow_transaction_id' => $borrow->id, 'handy_talky_id' => $htId]);
        HandyTalky::where('id', $htId)->update([
            'status' => 'borrowed',
            'location_id' => $this->destination_location_id,
        ]);
    }

    foreach ($this->selectedCharger as $chargerId) {
        BorrowItem::create(['borrow_transaction_id' => $borrow->id, 'charger_id' => $chargerId]);
        Charger::where('id', $chargerId)->update(['status' => 'borrowed']);
    }

    session()->flash('message', 'Peminjaman berhasil dicatat.');
    $this->reset(['step', 'employee_id', 'due_date', 'destination_location_id', 'purpose', 'loan_type', 'selectedHt', 'selectedCharger', 'notes', 'document']);;
    $this->borrow_date = now()->format('Y-m-d');
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
        @foreach (['Info Peminjam', 'Pilih Aset', 'Syarat & Upload'] as $index => $label)
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
            <h2 class="text-lg font-semibold mb-4">1. Info Peminjam</h2>

            <div class="space-y-4">
    <div>
        <x-input-label for="borrow_date" value="Tanggal Peminjaman" />
        <input type="date" id="borrow_date" wire:model="borrow_date" class="mt-1 block w-full rounded-control border-border text-sm">
        <x-input-error :messages="$errors->get('borrow_date')" class="mt-1" />
        <p class="text-xs text-text-secondary mt-1">Bisa diisi tanggal lampau untuk mencatat data peminjaman lama.</p>
    </div>

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
        <x-input-label for="destination_location_id" value="Lokasi Tujuan (OPD/Unit Penerima)" />
        <select id="destination_location_id" wire:model="destination_location_id" class="mt-1 block w-full rounded-control border-border text-sm">
            <option value="">-- Pilih Lokasi Tujuan --</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}">{{ $location->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('destination_location_id')" class="mt-1" />
        <p class="text-xs text-text-secondary mt-1">
            Kalau lokasi belum ada di daftar, tambahkan dulu lewat menu Master Data → Locations.
        </p>
    </div>

    <div>
        <x-input-label value="Jenis Peminjaman" />
        <div class="flex gap-4 mt-1 text-sm">
            <label class="flex items-center gap-1">
                <input type="radio" wire:model.live="loan_type" value="sementara"> Sementara (ada tenggat, mis. event)
            </label>
            <label class="flex items-center gap-1">
                <input type="radio" wire:model.live="loan_type" value="tetap"> Tetap / Stand-by (tanpa tenggat)
            </label>
        </div>
    </div>

@if ($loan_type === 'sementara')
    <div>
        <x-input-label for="due_date" value="Rencana Tanggal Kembali" />
        <input type="date" id="due_date" wire:model="due_date" class="mt-1 block w-full rounded-control border-border text-sm">
        <x-input-error :messages="$errors->get('due_date')" class="mt-1" />
    </div>
@endif

    <div>
        <x-input-label for="purpose" value="Keperluan / Tujuan Peminjaman" />
        <input type="text" id="purpose" wire:model="purpose" placeholder="Contoh: Kegiatan lapangan, siaga bencana, dll" class="mt-1block w-full rounded-control border-border text-sm">
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
            <h2 class="text-lg font-semibold mb-4">2. Pilih Aset</h2>

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
            <h2 class="text-lg font-semibold mb-4">3. Syarat & Konfirmasi</h2>

            <div>
                <x-input-label for="notes" value="Catatan (opsional)" />
                <textarea id="notes" wire:model="notes" rows="3" class="mt-1 block w-full rounded-control border-border text-sm" placeholder="Contoh: Untuk kegiatan lapangan"></textarea>
            </div>
                    <div class="mt-4">
                <x-input-label for="document" value="Upload Surat Permohonan Peminjaman (opsional)" />
                <input type="file" id="document" wire:model="document" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm border border-border rounded-control p-2">
                <x-input-error :messages="$errors->get('document')" class="mt-1" />
                <div wire:loading wire:target="document" class="text-xs text-text-secondary mt-1">Mengunggah file...</div>
                <p class="text-xs text-text-secondary mt-1">Format PDF/JPG/PNG, maksimal 5MB.</p>
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