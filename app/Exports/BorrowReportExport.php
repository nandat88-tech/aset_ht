<?php

namespace App\Exports;

use App\Models\BorrowTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BorrowReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $year = null,
        protected ?string $month = null,
        protected ?string $locationId = null,
    ) {}

    public function collection()
    {
        $query = BorrowTransaction::with('employee', 'destinationLocation', 'items');

        if ($this->year) {
            $query->whereYear('borrow_date', $this->year);
        }
        if ($this->month) {
            $query->whereMonth('borrow_date', $this->month);
        }
        if ($this->locationId) {
            $query->where('destination_location_id', $this->locationId);
        }

        return $query->latest('borrow_date')->get();
    }

        public function headings(): array
    {
        return ['Peminjam', 'Departemen', 'Lokasi Tujuan', 'Keperluan', 'Tanggal Pinjam', 'Jatuh Tempo', 'Jumlah HT', 'Jumlah Charger', 'Status'];
    }
    public function map($trx): array
    {
        $statusLabel = ['active' => 'Dipinjam', 'returned' => 'Selesai', 'returned_late' => 'Terlambat'][$trx->status] ?? $trx->status;
        return [
            $trx->employee->name,
            $trx->employee->department,
            $trx->destinationLocation->name ?? '-',
            $trx->purpose ?? '-',
            \Carbon\Carbon::parse($trx->borrow_date)->format('d-m-Y'),
            \Carbon\Carbon::parse($trx->due_date)->format('d-m-Y'),
            $trx->items->whereNotNull('handy_talky_id')->count(),
            $trx->items->whereNotNull('charger_id')->count(),
            $statusLabel,
        ];
    }
}