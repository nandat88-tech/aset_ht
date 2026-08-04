<?php

use App\Models\HandyTalky;
use App\Models\Location;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    #[Url(as: 'location')]
    public ?string $locationFilter = null;


    // Field-field form
    public string $serial_number = '';
    public string $inventory_number = '';
    public string $brand = '';
    public string $model = '';
    public string $frequency = '';
    public ?int $location_id = null;

    public function with(): array
    {
        return [
            'handyTalkies' => HandyTalky::query()
                ->when($this->search, function ($query) {
                    $query->where('serial_number', 'like', '%' . $this->search . '%')
                        ->orWhere('inventory_number', 'like', '%' . $this->search . '%')
                        ->orWhere('brand', 'like', '%' . $this->search . '%');
                })
                ->when($this->locationFilter, function ($query) {
                    if ($this->locationFilter === '__none__') {
                        $query->whereNull('location_id');
                    } else {
                        $query->whereHas('location', fn ($q) => $q->where('name', $this->locationFilter));
                    }
                })
                ->latest()
                ->paginate(10),
            'locations' => Location::orderBy('name')->get(),
        ];
    }

    public function clearLocationFilter(): void
    {
        $this->locationFilter = null;
        $this->resetPage();
    }

    public function openModal(): void
{
    abort_unless(auth()->user()->canEditData(), 403, 'Anda tidak memiliki akses untuk menambah data.');

    $this->reset(['serial_number', 'inventory_number', 'brand', 'model', 'frequency', 'location_id', 'editingId']);
    $this->showModal = true;
}

    public function edit(int $id): void
    {
        abort_unless(auth()->user()->canEditData(), 403, 'Anda tidak memiliki akses untuk mengedit data.');

        $ht = HandyTalky::findOrFail($id);

        $this->editingId = $ht->id;
        $this->serial_number = $ht->serial_number;
        $this->inventory_number = $ht->inventory_number;
        $this->brand = $ht->brand;
        $this->model = $ht->model;
        $this->frequency = $ht->frequency ?? '';
        $this->location_id = $ht->location_id;

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
{
    abort_unless(auth()->user()->canEditData(), 403, 'Anda tidak memiliki akses untuk menyimpan data.');

    $validated = $this->validate([
        'serial_number' => 'required|string|unique:handy_talkies,serial_number,' . $this->editingId,
        'inventory_number' => 'required|string|unique:handy_talkies,inventory_number,' . $this->editingId,
        'brand' => 'required|string',
        'model' => 'required|string',
        'frequency' => 'nullable|string',
        'location_id' => 'nullable|exists:locations,id',
    ]);

    if ($this->editingId) {
        HandyTalky::findOrFail($this->editingId)->update($validated);
        session()->flash('message', 'Data Handy Talky berhasil diperbarui.');
    } else {
        HandyTalky::create($validated);
        session()->flash('message', 'Data Handy Talky berhasil ditambahkan.');
    }

    $this->showModal = false;
}
public function markAsRepaired(int $id): void
{
    abort_unless(auth()->user()->canEditData(), 403, 'Anda tidak memiliki akses untuk mengubah status.');

    $diskominfo = \App\Models\Location::firstOrCreate(['name' => 'DISKOMINFO']);

    \App\Models\HandyTalky::where('id', $id)->update([
        'condition' => 'good',
        'status' => 'available',
        'location_id' => $diskominfo->id,
    ]);

    session()->flash('message', 'Unit berhasil diperbarui menjadi tersedia kembali.');
}
}; ?>

<div>
    @if (session('message'))
        <div class="mb-4 bg-green-50 text-success text-sm px-4 py-3 rounded-control">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div class="flex items-center gap-3 flex-wrap">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nomor seri, merk..."
                class="w-72 rounded-control border-border text-sm"
            >
            @if ($locationFilter)
                <span class="inline-flex items-center gap-2 bg-primary-light text-primary text-xs font-medium px-3 py-1.5 rounded-full">
                    Lokasi: {{ $locationFilter === '__none__' ? 'Tanpa Lokasi' : $locationFilter }}
                    <button wire:click="clearLocationFilter" class="hover:text-primary-dark font-bold">✕</button>
                </span>
            @endif
        </div>
        @if (!auth()->user()->isViewer())
    @if (auth()->user()->canEditData())
    <button wire:click="openModal" class="bg-primary text-white text-sm px-4 py-2 rounded-control hover:bg-primary-dark">
        + Tambah Data
    </button>
@endif
@endif
    </div>

    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Nomor Seri</th>
                    <th class="text-left px-4 py-3">Nomor Inventaris</th>
                    <th class="text-left px-4 py-3">Merk / Model</th>
                    <th class="text-left px-4 py-3">Lokasi</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($handyTalkies as $ht)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $ht->serial_number }}</td>
                        <td class="px-4 py-3">{{ $ht->inventory_number }}</td>
                        <td class="px-4 py-3">{{ $ht->brand }} {{ $ht->model }}</td>
                        <td class="px-4 py-3">{{ $ht->location?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="badge badge-{{ $ht->status === 'available' ? 'success' : ($ht->status === 'borrowed' ? 'info' : 'danger') }}">
                                {{ match($ht->status) {
                                    'available' => 'Tersedia',
                                    'borrowed' => 'Dipinjam',
                                    'damaged' => 'Rusak',
                                    'under_repair' => 'Perlu Perbaikan',
                                    default => $ht->status,
                                } }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                        @if (auth()->user()->canEditData())
                            <button wire:click="edit({{ $ht->id }})" class="text-primary hover:underline">Edit</button>
                            @if (in_array($ht->status, ['damaged', 'under_repair']))
                                <button wire:click="markAsRepaired({{ $ht->id }})"
                                    wire:confirm="Yakin unit ini sudah selesai diperbaiki dan siap dipakai kembali?"
                                    class="text-success hover:underline ml-3">
                                    Selesai Diperbaiki
                                </button>
                            @endif
    @else
        <span class="text-text-secondary text-xs">Lihat saja</span>
    @endif
</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-text-secondary">
                            Belum ada data Handy Talky.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $handyTalkies->links() }}
    </div>

    <!-- Modal Tambah Data -->
@if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="bg-white rounded-card shadow-card w-full max-w-lg">
            <form wire:submit="save" class="p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-4">
    {{ $editingId ? 'Edit Data Handy Talky' : 'Tambah Data Handy Talky' }}
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

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="brand" value="Brand" />
                            <x-text-input id="brand" wire:model="brand" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('brand')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="model" value="Model" />
                            <x-text-input id="model" wire:model="model" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('model')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="frequency" value="Frequency (opsional)" />
                        <x-text-input id="frequency" wire:model="frequency" class="mt-1 block w-full" />
                    </div>

                    <div>
                        <x-input-label for="location_id" value="Lokasi" />
                        <select id="location_id" wire:model="location_id" class="mt-1 block w-full rounded-control border-border text-sm">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-control text-sm border border-border hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-control text-sm bg-primary text-white hover:bg-primary-dark">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
</div>