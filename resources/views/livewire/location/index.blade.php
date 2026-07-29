<?php

use App\Models\Location;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $description = '';

    public function with(): array
    {
        return [
            'locations' => Location::query()
                ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->latest()
                ->paginate(10),
        ];
    }

    public function openModal(): void
    {
        $this->reset(['name', 'description', 'editingId']);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function edit(int $id): void
    {
        $location = Location::findOrFail($id);
        $this->editingId = $location->id;
        $this->name = $location->name;
        $this->description = $location->description ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($this->editingId) {
            Location::findOrFail($this->editingId)->update($validated);
            session()->flash('message', 'Lokasi berhasil diperbarui.');
        } else {
            Location::create($validated);
            session()->flash('message', 'Lokasi berhasil ditambahkan.');
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
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama lokasi..." class="w-72 rounded-control border-border text-sm">
        <button wire:click="openModal" class="bg-primary text-white text-sm px-4 py-2 rounded-control hover:bg-primary-dark">
            + Tambah Lokasi
        </button>
    </div>

    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Nama Lokasi</th>
                    <th class="text-left px-4 py-3">Deskripsi</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($locations as $location)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $location->name }}</td>
                        <td class="px-4 py-3">{{ $location->description ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="edit({{ $location->id }})" class="text-primary hover:underline">Edit</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-text-secondary">Belum ada data lokasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $locations->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="bg-white rounded-card shadow-card w-full max-w-lg">
                <form wire:submit="save" class="p-6">
                    <h2 class="text-lg font-semibold text-text-primary mb-4">
                        {{ $editingId ? 'Edit Lokasi' : 'Tambah Lokasi' }}
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" value="Nama Lokasi" />
                            <x-text-input id="name" wire:model="name" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="description" value="Deskripsi (opsional)" />
                            <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full rounded-control border-border text-sm"></textarea>
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