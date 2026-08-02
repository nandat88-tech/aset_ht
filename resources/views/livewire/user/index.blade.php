<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'operator';

    public function with(): array
    {
        return [
            'users' => User::latest()->paginate(10),
        ];
    }

    public function openModal(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->reset(['name', 'email', 'password', 'editingId']);
        $this->role = 'operator';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function edit(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $user = User::findOrFail($id);
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editingId,
            'role' => 'required|in:admin,operator,viewer',
        ];

        $rules['password'] = $this->editingId ? 'nullable|min:8' : 'required|min:8';

        $validated = $this->validate($rules);

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];
            if (!empty($validated['password'])) {
                $user->password = $validated['password'];
            }
            $user->save();
            session()->flash('message', 'Akun pengguna berhasil diperbarui.');
        } else {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'password' => $validated['password'],
            ]);
            session()->flash('message', 'Akun pengguna berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        // Placeholder untuk pengembangan mendatang (nonaktifkan akun tanpa hapus)
    }
}; ?>

<div>
    @if (session('message'))
        <div class="mb-4 bg-green-50 text-success text-sm px-4 py-3 rounded-control">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-end mb-4">
        <button wire:click="openModal" class="bg-primary text-white text-sm px-4 py-2 rounded-control hover:bg-primary-dark">
            + Tambah Pengguna
        </button>
    </div>

    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Email</th>
                    <th class="text-left px-4 py-3">Role</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="badge
                            @if($user->role === 'admin')
                                badge-info
                            @elseif($user->role === 'operator')
                                badge-success
                            @else
                                badge-warning
                            @endif
                            ">
                                @if($user->role === 'admin')
                                    Admin
                                @elseif($user->role === 'operator')
                                    Operator
                                @else
                                    Viewer
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="edit({{ $user->id }})" class="text-primary hover:underline">Edit</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-text-secondary">Belum ada data pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
            <div class="bg-white rounded-card shadow-card w-full max-w-lg">
                <form wire:submit="save" class="p-6">
                    <h2 class="text-lg font-semibold text-text-primary mb-4">
                        {{ $editingId ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="name" value="Nama" />
                            <x-text-input id="name" wire:model="name" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" type="email" wire:model="email" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label value="Role" />
                            <div class="flex gap-4 mt-1 text-sm">
                                <label class="flex items-center gap-1">
                                    <input type="radio" wire:model="role" value="admin"> Admin (akses penuh)
                                </label>
                                <label class="flex items-center gap-1">
                                    <input type="radio" wire:model="role" value="operator"> Operator (monitoring saja)
                                </label>
                                <label class="flex items-center gap-1">
                                    <input type="radio" wire:model="role" value="viewer">
                                    Viewer (hanya melihat data)
                                </label>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="password" :value="$editingId ? 'Kata Sandi Baru (opsional, kosongkan jika tidak diubah)' : 'Kata Sandi'" />
                            <x-text-input id="password" type="password" wire:model="password" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
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