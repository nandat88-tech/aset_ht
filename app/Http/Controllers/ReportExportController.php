<?php

namespace App\Http\Controllers;

use App\Exports\BorrowReportExport;
use App\Models\BorrowTransaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportController extends Controller
{
    public function excel(Request $request)
    {
        $export = new BorrowReportExport(
            $request->query('year'),
            $request->query('month'),
            $request->query('location'),
        );

        return Excel::download($export, 'laporan-peminjaman-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function pdf(Request $request)
    {
        $query = BorrowTransaction::with('employee', 'destinationLocation', 'items');

        if ($request->query('year')) {
            $query->whereYear('borrow_date', $request->query('year'));
        }
        if ($request->query('month')) {
            $query->whereMonth('borrow_date', $request->query('month'));
        }
        if ($request->query('location')) {
            $query->where('destination_location_id', $request->query('location'));
        }

        $transactions = $query->latest('borrow_date')->get();

        $pdf = Pdf::loadView('exports.borrow-report-pdf', [
            'transactions' => $transactions,
            'generatedAt' => now()->format('d M Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
    }
}