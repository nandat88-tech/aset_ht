<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$locationIdsToDelete = [1, 12, 13, 14, 15, 16, 17];
$employeeIdsToDelete = [4, 5, 6, 7, 10, 11];

echo "=== LOKASI YANG AKAN DIHAPUS ===\n";
foreach ($locationIdsToDelete as $id) {
    $loc = \App\Models\Location::find($id);
    if ($loc) echo "- ID $id: $loc->name\n";
}

echo "\n=== PEGAWAI YANG AKAN DIHAPUS ===\n";
foreach ($employeeIdsToDelete as $id) {
    $emp = \App\Models\Employee::find($id);
    if ($emp) echo "- ID $id: $emp->name\n";
}

echo "\n=== HT/CHARGER YANG SEDANG BERLOKASI DI LOKASI TERSEBUT ===\n";
$htCount = \App\Models\HandyTalky::whereIn('location_id', $locationIdsToDelete)->count();
echo "Handy Talky: $htCount unit (akan dipindahkan ke DISKOMINFO)\n";

echo "\n=== TRANSAKSI PEMINJAMAN TERKAIT ===\n";
$borrowCount = \App\Models\BorrowTransaction::whereIn('employee_id', $employeeIdsToDelete)
    ->orWhereIn('destination_location_id', $locationIdsToDelete)
    ->count();
echo "Total transaksi peminjaman yang akan ikut dihapus: $borrowCount\n";

$activeCount = \App\Models\BorrowTransaction::whereIn('employee_id', $employeeIdsToDelete)
    ->orWhereIn('destination_location_id', $locationIdsToDelete)
    ->where('status', 'active')
    ->count();
echo "Dari jumlah itu, yang masih berstatus AKTIF (belum dikembalikan): $activeCount\n";
