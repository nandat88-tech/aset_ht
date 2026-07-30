<?php

namespace App\Imports;

use App\Models\HandyTalky;
use App\Models\Location;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class HandyTalkyImport implements ToCollection, WithStartRow
{
    public int $imported = 0;
    public int $skipped = 0;
    public array $skippedRows = [];

    public function startRow(): int
    {
        return 6;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + $this->startRow();

            $serialNumber = trim((string) ($row[1] ?? ''));
            $nup = trim((string) ($row[2] ?? ''));
            $kondisi = trim((string) ($row[4] ?? ''));
            $posisi = trim((string) ($row[6] ?? ''));

            // Lewati baris kosong
            if (empty($serialNumber)) {
                continue;
            }

            // Deteksi baris yang sebenarnya adalah header berulang atau judul
            // (serial number seharusnya mengandung angka; kalau tidak, kemungkinan bukan data asli)
            if (!preg_match('/\d/', $serialNumber)) {
                $this->skipped++;
                $this->skippedRows[] = "Baris {$rowNumber}: '{$serialNumber}' (bukan format serial number)";
                continue;
            }

            // Lewati jika serial number sudah ada di database (hindari duplikat)
            if (HandyTalky::where('serial_number', $serialNumber)->exists()) {
                $this->skipped++;
                $this->skippedRows[] = "Baris {$rowNumber}: '{$serialNumber}' (sudah ada di database)";
                continue;
            }

            try {
                $condition = str_contains(strtolower($kondisi), 'baik') ? 'good' : 'damaged';

                $locationId = null;
                if (!empty($posisi)) {
                    $location = Location::firstOrCreate(['name' => $posisi]);
                    $locationId = $location->id;
                }

                // Tentukan status: rusak selalu "damaged"; kalau baik, tergantung lokasi
                if ($condition === 'damaged') {
                    $status = 'damaged';
                } elseif (strtoupper(trim($posisi)) === 'DISKOMINFO') {
                    $status = 'available';
                } else {
                    $status = 'borrowed';
                }

                HandyTalky::create([
                    'serial_number' => $serialNumber,
                    'inventory_number' => $nup !== '' ? $nup : $serialNumber,
                    'brand' => 'Hytera',
                    'model' => 'Digital Radio',
                    'condition' => $condition,
                    'status' => $status,
                    'location_id' => $locationId,
                ]);

                $this->imported++;
            } catch (\Throwable $e) {
                $this->skipped++;
                $this->skippedRows[] = "Baris {$rowNumber}: gagal disimpan ({$e->getMessage()})";
            }
        }
    }
}