<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<aside class="w-64 min-h-screen bg-sidebar-bg text-sidebar-text flex flex-col">
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-white/10">
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2 text-white font-bold">
            <x-application-logo class="h-8 w-auto fill-current text-white" />
            <span class="text-sm">Diskominfo</span>
        </a>
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">
        <div>
            <a href="{{ route('dashboard') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
                    {{ request()->routeIs('dashboard') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
                Dashboard
            </a>
        </div>

        <div>
            <p class="px-3 text-xs uppercase tracking-wide text-sidebar-text/50 mb-2">Master Data</p>
            <a href="{{ route('handy-talky.index') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
                    {{ request()->routeIs('handy-talky.index') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
                Handy Talky
            </a>
            <a href="{{ route('handy-talky.import') }}" wire:navigate
    class="flex items-center gap-3 pl-8 pr-3 py-1.5 rounded-control text-xs
        {{ request()->routeIs('handy-talky.import') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10 text-sidebar-text/70' }}">
    ↳ Import Excel
</a>
            <a href="{{ route('chargers.index') }}" wire:navigate
    class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
        {{ request()->routeIs('chargers.index') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
    Charger
</a>
            <a href="{{ route('locations.index') }}" wire:navigate
    class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
        {{ request()->routeIs('locations.index') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
    Locations
</a>
            <a href="{{ route('employees.index') }}" wire:navigate
    class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
        {{ request()->routeIs('employees.index') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
    Employees
</a>
        </div>

        <div>
            <p class="px-3 text-xs uppercase tracking-wide text-sidebar-text/50 mb-2">Transaksi</p>
            <a href="{{ route('borrowing.index') }}" wire:navigate
    class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
        {{ request()->routeIs('borrowing.index') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
    Borrowing
</a>
            <a href="{{ route('returning.index') }}" wire:navigate
    class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
        {{ request()->routeIs('returning.*') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
    Returning
</a>
        <a href="{{ route('late-returns.index') }}" wire:navigate
        class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
            {{ request()->routeIs('late-returns.index') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
        Late Returns
        </a>
        </div>

        <div>
            <p class="px-3 text-xs uppercase tracking-wide text-sidebar-text/50 mb-2">Reports</p>
            <a href="{{ route('reports.index') }}" wire:navigate
                class="flex items-center gap-3 px-3 py-2 rounded-control text-sm
                    {{ request()->routeIs('reports.index') ? 'bg-primary-light text-primary font-semibold' : 'hover:bg-white/10' }}">
                Laporan
            </a>
        </div>

        <div>
            <p class="px-3 text-xs uppercase tracking-wide text-sidebar-text/50 mb-2">Administration</p>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-control text-sm hover:bg-white/10">Users</a>
            <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-3 px-3 py-2 rounded-control text-sm hover:bg-white/10">Profile</a>
        </div>
    </nav>

    <!-- User & Logout -->
    <div class="px-3 py-4 border-t border-white/10">
        <div class="px-3 mb-2 text-sm text-white">{{ auth()->user()->name }}</div>
        <button wire:click="logout" class="w-full text-left px-3 py-2 rounded-control text-sm text-red-300 hover:bg-white/10">
            Log Out
        </button>
    </div>
</aside>