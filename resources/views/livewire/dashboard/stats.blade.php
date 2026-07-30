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
            'htGood' => HandyTalky::where('condition', 'good')->count(),
            'htConditionDamaged' => HandyTalky::where('condition', '!=', 'good')->count(),
            'locationLabels' => HandyTalky::with('location')->get()
                ->groupBy(fn ($ht) => $ht->location?->name ?? 'Tanpa Lokasi')
                ->map->count()
                ->keys(),
            'locationCounts' => HandyTalky::with('location')->get()
                ->groupBy(fn ($ht) => $ht->location?->name ?? 'Tanpa Lokasi')
                ->map->count()
                ->values(),
        ];
    }
}; ?>

<div>
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

<!-- Grafik Kondisi HT & Distribusi Lokasi -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
<div class="bg-card rounded-card shadow-card p-4">
    <h3 class="text-sm font-semibold text-text-primary mb-3">Kondisi Handy Talky</h3>
    <div class="max-w-xs mx-auto" style="height: 220px;" wire:ignore
        x-data="{
            chart: null,
            init() {
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Baik', 'Rusak'],
                        datasets: [{
                            data: [{{ $htGood }}, {{ $htConditionDamaged }}],
                            backgroundColor: ['#16A34A', '#DC2626'],
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
        }">
       <canvas x-ref="canvas"></canvas>
    </div>
</div>

<div class="bg-card rounded-card shadow-card p-4">
    <h3 class="text-sm font-semibold text-text-primary mb-3">Distribusi HT per Lokasi</h3>
    <div style="height: 220px;" wire:ignore
    x-data='{
        chart: null,
        init() {
            this.chart = new Chart(this.$refs.barCanvas, {
                type: "bar",
                data: {
                    labels: @json($locationLabels),
                    datasets: [{
                        label: "Jumlah HT",
                        data: @json($locationCounts),
                        backgroundColor: "#2563EB"
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }
    }'>
    <canvas x-ref="barCanvas"></canvas>
</div>
</div>
</div>
