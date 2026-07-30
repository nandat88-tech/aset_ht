<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::view('profile', 'profile')->name('profile');
    Route::view('handy-talky', 'handy-talky')->name('handy-talky.index');
    Route::view('handy-talky/import', 'handy-talky-import')->name('handy-talky.import');
    Route::view('locations', 'locations')->name('locations.index');
    Route::view('chargers', 'chargers')->name('chargers.index');
    Route::view('employees', 'employees')->name('employees.index');
    Route::view('borrowing', 'borrowing')->name('borrowing.index');
    Route::view('returning', 'returning')->name('returning.index');
    Route::get('returning/{transactionId}/process', function (int $transactionId) {
        return view('returning-process', ['transactionId' => $transactionId]);
    })->name('returning.process');
});

require __DIR__.'/auth.php';
