<?php

use App\Models\HandyTalky;
use App\Models\Charger;
use App\Models\BorrowTransaction;
use Livewire\Volt\Component;

new class extends Component
{
    public function with(): array
    {
        return [
            'totalHt' => HandyTalky::count(),
            'htAvailable' => HandyTalky::where('status', 'available')->count(),
            'htBorrowed' => HandyTalky::where('status', 'borrowed')->count(),
            'htUnderRepair' => HandyTalky::where('status', 'under_repair')->count(),
            'htDamaged' => HandyTalky::where('status', 'damaged')->count(),
            'totalCharger' => Charger::count(),
            'chargerAvailable' => Charger::where('status', 'available')->count(),
            'chargerDamaged' => Charger::where('status', 'damaged')->count(),
            'lateReturns' => BorrowTransaction::where('status', 'active')
                ->where('due_date', '<', now())
                ->count(),
        ];
    }
}; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">Total HT</p>
            <p class="text-2xl font-bold text-text-primary">{{ $totalHt }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-primary-light text-primary">📻</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">HT Available</p>
            <p class="text-2xl font-bold text-text-primary">{{ $htAvailable }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-green-100 text-success">✅</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">HT Borrowed</p>
            <p class="text-2xl font-bold text-text-primary">{{ $htBorrowed }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-blue-100 text-info">🤝</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">HT Under Repair</p>
            <p class="text-2xl font-bold text-text-primary">{{ $htUnderRepair }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-amber-100 text-warning">🔧</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">HT Damaged</p>
            <p class="text-2xl font-bold text-text-primary">{{ $htDamaged }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-red-100 text-danger">⚠️</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">Total Charger</p>
            <p class="text-2xl font-bold text-text-primary">{{ $totalCharger }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-primary-light text-primary">🔌</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">Charger Available</p>
            <p class="text-2xl font-bold text-text-primary">{{ $chargerAvailable }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-green-100 text-success">✅</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">Late Returns</p>
            <p class="text-2xl font-bold text-text-primary">{{ $lateReturns }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-red-100 text-danger">⏰</div>
    </div>
</div>