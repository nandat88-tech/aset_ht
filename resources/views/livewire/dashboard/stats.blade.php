<?php

use App\Models\HandyTalky;
use App\Models\Charger;
use App\Models\BorrowTransaction;
use App\Models\ReturnTransaction;
use Livewire\Volt\Component;
use Illuminate\Support\Carbon;

new class extends Component
{
    public string $period = '30';

    public function with(): array
    {
        return [
            'totalHt' => HandyTalky::count(),
            'htTersedia' => HandyTalky::where('status', 'available')->count(),
            'htDipinjam' => HandyTalky::where('status', 'borrowed')->count(),
            'htPerluPerbaikan' => HandyTalky::where('status', 'under_repair')->count(),
            'htRusak' => HandyTalky::where('status', 'damaged')->count(),
            'totalCharger' => Charger::count(),
            'chargerTersedia' => Charger::where('status', 'available')->count(),
            'chargerRusak' => Charger::where('status', 'damaged')->count(),
            'Keterlambatan' => BorrowTransaction::where('status', 'active')
                ->where('loan_type', 'sementara')
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
            'recentActivities' => $this->getRecentActivities(),
            'lateReturns' => $this->getLateReturns(),
            'monthlyTrend' => $this->getMonthlyTrend(),
            'instansiBreakdown' => $this->getInstansiBreakdown(),
        ];
    }

    private function periodStartDate(): ?Carbon
    {
        return match ($this->period) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            default => null,
        };
    }

    private function getRecentActivities()
    {
        $start = $this->periodStartDate();

        $borrowsQuery = BorrowTransaction::with('employee')->latest('borrow_date');
        $returnsQuery = ReturnTransaction::with('borrowTransaction.employee')->latest('return_date');

        if ($start) {
            $borrowsQuery->where('borrow_date', '>=', $start);
            $returnsQuery->where('return_date', '>=', $start);
        }

        $borrows = $borrowsQuery->take(10)->get()->map(function ($trx) {
            return [
                'type' => 'borrow',
                'label' => 'Peminjaman',
                'employee' => $trx->employee?->name ?? '-',
                'date' => $trx->borrow_date,
                'icon' => '🤝',
                'color' => 'text-info',
            ];
        });

        $returns = $returnsQuery->take(10)->get()->map(function ($trx) {
            return [
                'type' => 'return',
                'label' => 'Pengembalian',
                'employee' => $trx->borrowTransaction?->employee?->name ?? '-',
                'date' => $trx->return_date,
                'icon' => '📦',
                'color' => 'text-success',
            ];
        });

        return $borrows->concat($returns)
            ->sortByDesc('date')
            ->take(6)
            ->values();
    }

    private function getLateReturns()
    {
        return BorrowTransaction::with(['employee', 'items.handyTalky'])
            ->where('status', 'active')
            ->where('loan_type', 'sementara')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->take(5)
            ->get()
            ->map(function ($trx) {
                $units = $trx->items
                    ->pluck('handyTalky.inventory_number')
                    ->filter()
                    ->implode(', ');

                return [
                    'id' => $trx->id,
                    'employee' => $trx->employee?->name ?? '-',
                    'units' => $units ?: '-',
                    'daysLate' => now()->diffInDays($trx->due_date),
                ];
            });
    }

    private function getMonthlyTrend()
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $counts = $months->map(function ($month) {
            return BorrowTransaction::whereBetween('borrow_date', [
                $month->copy()->startOfMonth(),
                $month->copy()->endOfMonth(),
            ])->count();
        });

        return [
            'labels' => $months->map(fn ($m) => $m->translatedFormat('M Y'))->values(),
            'counts' => $counts->values(),
        ];
    }

    private function getInstansiBreakdown()
    {
        $start = $this->periodStartDate();

        $query = BorrowTransaction::with('employee');
        if ($start) {
            $query->where('borrow_date', '>=', $start);
        }

        return $query->get()
            ->groupBy(fn ($trx) => $trx->employee?->department ?? 'Tanpa Instansi')
            ->map->count()
            ->sortDesc()
            ->take(5);
    }
}; ?>

<div>
{{-- Greeting & Filter Periode --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div>
        <h2 class="text-lg font-semibold text-text-primary">Selamat datang, {{ auth()->user()->name }} 👋</h2>
        <p class="text-xs text-text-secondary">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <label class="text-xs text-text-secondary">Periode:</label>
        <select wire:model.live="period" class="text-xs rounded-control border-gray-200 py-1.5 pr-8">
            <option value="7">7 Hari Terakhir</option>
            <option value="30">30 Hari Terakhir</option>
            <option value="all">Semua</option>
        </select>
    </div>
</div>

{{-- Tombol Shortcut --}}
@if (auth()->user()->canEditData())
<div class="flex flex-wrap gap-3 mb-4">
    <a href="{{ route('borrowing.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-dark transition-colors">
        <span>➕</span> Peminjaman Baru
    </a>
    <a href="{{ route('returning.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-white border border-gray-200 text-text-primary text-sm font-medium hover:bg-gray-50 transition-colors">
        <span>↩️</span> Pengembalian Baru
    </a>
</div>
@endif

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
            <p class="text-text-secondary text-xs font-medium mb-1">HT Tersedia</p>
            <p class="text-2xl font-bold text-text-primary">{{ $htTersedia }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-green-100 text-success">✅</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">HT Dipinjam</p>
            <p class="text-2xl font-bold text-text-primary">{{ $htDipinjam }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-blue-100 text-info">🤝</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">HT Perlu Perbaikan</p>
            <p class="text-2xl font-bold text-text-primary">{{ $htPerluPerbaikan }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-amber-100 text-warning">🔧</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">HT Rusak</p>
            <p class="text-2xl font-bold text-text-primary">{{ $htRusak }}</p>
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
            <p class="text-text-secondary text-xs font-medium mb-1">Charger Tersedia</p>
            <p class="text-2xl font-bold text-text-primary">{{ $chargerTersedia }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-green-100 text-success">✅</div>
    </div>

    <div class="bg-card rounded-card shadow-card p-4 flex items-center justify-between">
        <div>
            <p class="text-text-secondary text-xs font-medium mb-1">Keterlambatan</p>
            <p class="text-2xl font-bold text-text-primary">{{ $Keterlambatan }}</p>
        </div>
        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-red-100 text-danger">⏰</div>
    </div>
</div>

<div class="mt-4 bg-card rounded-card shadow-card border-l-4 {{ $lateReturns->isNotEmpty() ? 'border-danger' : 'border-success' }} p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-text-primary">⏰ HT Telat Dikembalikan</h3>
        <a href="{{ route('late-returns.index') }}" class="text-xs text-primary hover:underline">Lihat semua →</a>
    </div>
    @if ($lateReturns->isNotEmpty())
        <div class="divide-y divide-gray-100">
            @foreach ($lateReturns as $late)
                <div class="flex items-center justify-between py-2.5 first:pt-0">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-text-primary truncate">{{ $late['employee'] }}</p>
                        <p class="text-xs text-text-secondary truncate">Unit: {{ $late['units'] }}</p>
                    </div>
                    <span class="shrink-0 ml-3 px-2 py-1 rounded-full bg-red-100 text-danger text-xs font-semibold">
                        {{ $late['daysLate'] }} hari
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex items-center gap-2 py-3">
            <span class="text-lg">✅</span>
            <p class="text-xs text-text-secondary">Tidak ada keterlambatan saat ini. Semua pengembalian tepat waktu.</p>
        </div>
    @endif
</div>

<!-- Baris 1: Kondisi HT & Aktivitas Terbaru -->
<div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
<div class="bg-card rounded-card shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200 p-4">
    <h3 class="text-sm font-semibold text-text-primary mb-3">Kondisi Handy Talky</h3>
    <div style="height: 260px;" wire:ignore wire:key="chart-kondisi-{{ $htGood }}-{{ $htConditionDamaged }}"
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
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        }">
       <canvas x-ref="canvas"></canvas>
    </div>
</div>

<div class="bg-card rounded-card shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200 p-4">
    <h3 class="text-sm font-semibold text-text-primary mb-3">Aktivitas Terbaru</h3>
    <div class="divide-y divide-gray-100 overflow-y-auto" style="height: 260px;" wire:key="activities-{{ $period }}">
        @forelse ($recentActivities as $activity)
            <div class="flex items-center gap-3 py-2.5 first:pt-0">
                <div class="h-8 w-8 rounded-full flex items-center justify-center bg-gray-100 {{ $activity['color'] }} text-sm shrink-0">
                    {{ $activity['icon'] }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-text-primary truncate">
                        {{ $activity['label'] }} — {{ $activity['employee'] }}
                    </p>
                    <p class="text-xs text-text-secondary">
                        {{ \Carbon\Carbon::parse($activity['date'])->translatedFormat('d M Y, H:i') }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-xs text-text-secondary text-center mt-8">Tidak ada aktivitas pada periode ini.</p>
        @endforelse
    </div>
</div>
</div>

<!-- Baris 2: Distribusi HT per Lokasi & Status HT -->
<div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
<div class="bg-card rounded-card shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200 p-4">
    <h3 class="text-sm font-semibold text-text-primary mb-3">Distribusi HT per Lokasi <span class="text-xs font-normal text-text-secondary">(klik batang untuk detail)</span></h3>
    <div style="height: 260px;" wire:ignore wire:key="chart-lokasi-{{ $locationCounts->sum() }}-{{ $locationLabels->count() }}"
         x-data='{
        chart: null,
        init() {
            const labels = @json($locationLabels);
            this.chart = new Chart(this.$refs.barCanvas, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Jumlah HT",
                        data: @json($locationCounts),
                        backgroundColor: "#2563EB"
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onHover: (event, elements) => {
                        event.native.target.style.cursor = elements.length ? "pointer" : "default";
                    },
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            const label = labels[elements[0].index];
                            const filterValue = label === "Tanpa Lokasi" ? "__none__" : label;
                            window.location.href = "{{ route('handy-talky.index') }}?location=" + encodeURIComponent(filterValue);
                        }
                    },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }
    }'>
    <canvas x-ref="barCanvas"></canvas>
</div>
</div>
<div class="bg-card rounded-card shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200 p-4">
    <h3 class="text-sm font-semibold text-text-primary mb-3">Status Handy Talky</h3>
    <div style="height: 260px;" wire:ignore wire:key="chart-status-{{ $htTersedia }}-{{ $htDipinjam }}-{{ $htPerluPerbaikan }}-{{ $htRusak }}"
        x-data="{
            chart: null,
            init() {
                this.chart = new Chart(this.$refs.statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Tersedia', 'Dipinjam', 'Perlu Perbaikan', 'Rusak'],
                        datasets: [{
                            data: [{{ $htTersedia }}, {{ $htDipinjam }}, {{ $htPerluPerbaikan }}, {{ $htRusak }}],
                            backgroundColor: ['#16A34A', '#2563EB', '#D97706', '#DC2626'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }
        }">
       <canvas x-ref="statusCanvas"></canvas>
    </div>
</div>
</div>

<!-- Baris 3: Tren Peminjaman & Ringkasan per Instansi -->
<div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
<div class="bg-card rounded-card shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200 p-4">
    <h3 class="text-sm font-semibold text-text-primary mb-3">Tren Peminjaman per Bulan</h3>
    <div style="height: 260px;" wire:ignore wire:key="chart-trend-{{ implode('-', $monthlyTrend['counts']->toArray()) }}"
         x-data='{
        chart: null,
        init() {
            this.chart = new Chart(this.$refs.trendCanvas, {
                type: "line",
                data: {
                    labels: @json($monthlyTrend['labels']),
                    datasets: [{
                        label: "Jumlah Peminjaman",
                        data: @json($monthlyTrend['counts']),
                        borderColor: "#2563EB",
                        backgroundColor: "rgba(37,99,235,0.1)",
                        fill: true,
                        tension: 0.3
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
    <canvas x-ref="trendCanvas"></canvas>
</div>
</div>

<div class="bg-card rounded-card shadow-card hover:shadow-card-hover hover:-translate-y-0.5 transition-all duration-200 p-4">
    <h3 class="text-sm font-semibold text-text-primary mb-3">Ringkasan per Instansi</h3>
    <div style="height: 260px; overflow-y: auto;" wire:key="instansi-{{ $period }}">
        @forelse ($instansiBreakdown as $instansi => $count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <p class="text-xs font-medium text-text-primary">{{ $instansi }}</p>
                <span class="text-xs font-semibold text-primary">{{ $count }} peminjaman</span>
            </div>
        @empty
            <p class="text-xs text-text-secondary text-center mt-8">Tidak ada data pada periode ini.</p>
        @endforelse
    </div>
</div>
</div>
</div>