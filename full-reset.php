<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

DB::statement('SET FOREIGN_KEY_CHECKS=0');

echo "Langkah 1: Menghapus semua riwayat transaksi (ReturnItem, ReturnTransaction, BorrowItem, BorrowTransaction)...\n";
DB::table('return_items')->truncate();
DB::table('return_transactions')->truncate();
DB::table('borrow_items')->truncate();
DB::table('borrow_transactions')->truncate();
echo "- Selesai.\n";

echo "\nLangkah 2: Reset semua Handy Talky ke kondisi baik, tersedia, lokasi DISKOMINFO...\n";
$diskominfo = \App\Models\Location::firstOrCreate(['name' => 'DISKOMINFO']);
\App\Models\HandyTalky::query()->update([
    'condition' => 'good',
    'status' => 'available',
    'location_id' => $diskominfo->id,
]);
echo "- Selesai.\n";

echo "\nLangkah 3: Reset semua Charger ke kondisi baik, tersedia...\n";
\App\Models\Charger::query()->update([
    'condition' => 'good',
    'status' => 'available',
]);
echo "- Selesai.\n";

echo "\nLangkah 4: Menghapus Pegawai fiktif...\n";
$keepEmployeeNames = ['Ajudan Walikota(fauzan)', 'Ajudan Sekda'];
$deletedEmployees = \App\Models\Employee::whereNotIn('name', $keepEmployeeNames)->get();
foreach ($deletedEmployees as $e) {
    echo "- Menghapus pegawai: $e->name\n";
}
\App\Models\Employee::whereNotIn('name', $keepEmployeeNames)->delete();

echo "\nLangkah 5: Menghapus Lokasi fiktif...\n";
$keepLocationNames = ['DISKOMINFO', 'Badan Penanggulangan Bencana Daerah', 'Ajudan Walikota(fauzan)', 'Ajudan Sekda'];
$deletedLocations = \App\Models\Location::whereNotIn('name', $keepLocationNames)->get();
foreach ($deletedLocations as $l) {
    echo "- Menghapus lokasi: $l->name\n";
}
\App\Models\Location::whereNotIn('name', $keepLocationNames)->delete();

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n=== SELESAI ===\n";
echo "Lokasi tersisa: " . \App\Models\Location::count() . "\n";
echo "Pegawai tersisa: " . \App\Models\Employee::count() . "\n";
echo "Handy Talky tersedia: " . \App\Models\HandyTalky::where('status', 'available')->count() . " / " . \App\Models\HandyTalky::count() . "\n";
echo "Charger tersedia: " . \App\Models\Charger::where('status', 'available')->count() . " / " . \App\Models\Charger::count() . "\n";
echo "Total transaksi peminjaman: " . \App\Models\BorrowTransaction::count() . "\n";
