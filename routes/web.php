<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Semua user yang login
Route::middleware(['auth'])->group(function () {

    Route::view('profile', 'profile')->name('profile');

    Route::view('handy-talky', 'handy-talky')->name('handy-talky.index');

    Route::view('reports', 'reports')->name('reports.index');

});

// Khusus Operator + Admin
Route::middleware(['auth', 'operator'])->group(function () {

    Route::view('locations', 'locations')->name('locations.index');

    Route::view('chargers', 'chargers')->name('chargers.index');

    Route::view('employees', 'employees')->name('employees.index');

    Route::view('borrowing', 'borrowing')->name('borrowing.index');

    Route::view('returning', 'returning')->name('returning.index');

    Route::get('returning/{transactionId}/process', function (int $transactionId) {
        return view('returning-process', [
            'transactionId' => $transactionId
        ]);
    })->name('returning.process');

    Route::view('late-returns', 'late-returns')->name('late-returns.index');

});
// Khusus Admin
Route::middleware(['auth', 'admin'])->group(function () {

    Route::view('handy-talky/import', 'handy-talky-import')
    ->name('handy-talky.import');

    Route::view('users', 'users')->name('users.index');

    Route::get('reports/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'pdf'])
        ->name('reports.export-pdf');

    Route::get('reports/export/excel', [\App\Http\Controllers\ReportExportController::class, 'excel'])
        ->name('reports.export-excel');

});
require __DIR__.'/auth.php';
