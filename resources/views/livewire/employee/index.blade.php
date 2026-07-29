<?php

use App\Models\Employee;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $department = '';

    public function with(): array
    {
        return [
            'employees' => Employee::query()
                ->when($this->search, function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('department', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),
        ];
    }

    public function openModal(): void
    {
        $this->reset(['name', 'department', 'editingId']);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function edit(int $id): void
    {
        $employee = Employee::findOrFail($id);
        $this->editingId = $employee->id;
        $this->name = $employee->name;
        $this->department = $employee->department;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string',
            'department' => 'required|string',
        ]);

        if ($this->editingId) {
            Employee::findOrFail($this->editingId)->update($validated);
            session()->flash('message', 'Data pegawai berhasil diperbarui.');
        } else {
            Employee::create($validated);
            session()->flash('message', 'Data pegawai berhasil ditambahkan.');
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
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, OPD..." class="w-72 rounded-control border-border text-sm">
        <button wire:click="openModal" class="bg-primary text-white text-sm px-4 py-2 rounded-control hover:bg-primary-dark">
            + Tambah Pegawai
        </button>
    </div>

    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">OPD / Departemen</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($employees as $employee)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $employee->name }}</td>
                        <td class="px-4 py-3">{{ $employee->department }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="edit({{ $employee->id }})" class="text-primary hover:underline">Edit</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-text-secondary">Belum ada data pegawai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $employees->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="bg-white rounded-card shadow-card w-full max-w-lg">
                <form wire:submit="save" class="p-6">
                    <h2 class="text-lg font-semibold text-text-primary mb-4">
                        {{ $editingId ? 'Edit Pegawai' : 'Tambah Pegawai' }}
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" value="Nama" />
                            <x-text-input id="name" wire:model="name" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="department" value="OPD / Departemen" />
                            <x-text-input id="department" wire:model="department" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('department')" class="mt-1" />
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