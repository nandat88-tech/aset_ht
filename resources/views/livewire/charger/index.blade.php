<?php

use App\Models\Charger;
use App\Models\HandyTalky;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $serial_number = '';
    public string $inventory_number = '';
    public ?int $handy_talky_id = null;

    public function with(): array
    {
        return [
            'chargers' => Charger::query()
                ->when($this->search, function ($q) {
                    $q->where('serial_number', 'like', '%' . $this->search . '%')
                        ->orWhere('inventory_number', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),
            'handyTalkies' => HandyTalky::orderBy('serial_number')->get(),
        ];
    }

    public function openModal(): void
    {
        $this->reset(['serial_number', 'inventory_number', 'handy_talky_id', 'editingId']);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function edit(int $id): void
    {
        $charger = Charger::findOrFail($id);
        $this->editingId = $charger->id;
        $this->serial_number = $charger->serial_number;
        $this->inventory_number = $charger->inventory_number;
        $this->handy_talky_id = $charger->handy_talky_id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'serial_number' => 'required|string|unique:chargers,serial_number,' . $this->editingId,
            'inventory_number' => 'required|string|unique:chargers,inventory_number,' . $this->editingId,
            'handy_talky_id' => 'nullable|exists:handy_talkies,id',
        ]);

        if ($this->editingId) {
            Charger::findOrFail($this->editingId)->update($validated);
            session()->flash('message', 'Data Charger berhasil diperbarui.');
        } else {
            Charger::create($validated);
            session()->flash('message', 'Data Charger berhasil ditambahkan.');
        }

        $this->showModal = false;
    }
}; ?>

<div>
    @if (session('message'))
        <div class="mb-4 bg-green-50 text-success text-sm px-4 py-3 rounded-control">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor seri..." class="w-72 rounded-control border-border text-sm">
        <button wire:click="openModal" class="bg-primary text-white text-sm px-4 py-2 rounded-control hover:bg-primary-dark">
            + Tambah Charger
        </button>
    </div>

    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Nomor Seri</th>
                    <th class="text-left px-4 py-3">Nomor Inventaris</th>
                    <th class="text-left px-4 py-3">HT Terkait</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($chargers as $charger)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $charger->serial_number }}</td>
                        <td class="px-4 py-3">{{ $charger->inventory_number }}</td>
                        <td class="px-4 py-3">{{ $charger->handyTalky?->serial_number ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="badge badge-{{ $charger->status === 'available' ? 'success' : ($charger->status === 'borrowed' ? 'info' : 'danger') }}">
                                {{ match($charger->status) {
                                    'available' => 'Tersedia',
                                    'borrowed' => 'Dipinjam',
                                    'damaged' => 'Rusak',
                                    'under_repair' => 'Perlu Perbaikan',
                                    default => $charger->status,
                                } }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="edit({{ $charger->id }})" class="text-primary hover:underline">Edit</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-text-secondary">Belum ada data charger.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $chargers->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="bg-white rounded-card shadow-card w-full max-w-lg">
                <form wire:submit="save" class="p-6">
                    <h2 class="text-lg font-semibold text-text-primary mb-4">
                        {{ $editingId ? 'Edit Charger' : 'Tambah Charger' }}
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="serial_number" value="Nomor Seri" />
                            <x-text-input id="serial_number" wire:model="serial_number" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('serial_number')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="inventory_number" value="Nomor Inventaris" />
                            <x-text-input id="inventory_number" wire:model="inventory_number" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('inventory_number')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="handy_talky_id" value="HT Terkait (opsional)" />
                            <select id="handy_talky_id" wire:model="handy_talky_id" class="mt-1 block w-full rounded-control border-border text-sm">
                                <option value="">-- Tidak ada --</option>
                                @foreach ($handyTalkies as $ht)
                                    <option value="{{ $ht->id }}">{{ $ht->serial_number }} ({{ $ht->brand }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-control text-sm border border-border hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-control text-sm bg-primary text-white hover:bg-primary-dark">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>