<?php

use App\Imports\HandyTalkyImport;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component
{
    use WithFileUploads;

    public $file = null;
    public ?string $resultMessage = null;
    public array $skippedDetails = [];

    public function import(): void
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        $import = new HandyTalkyImport();
        Excel::import($import, $this->file->getRealPath());

        $this->resultMessage = "Berhasil: {$import->imported} data ditambahkan. Dilewati: {$import->skipped} data.";
        $this->skippedDetails = $import->skippedRows;
        $this->file = null;
    }
}; ?>

<div class="max-w-xl">
    <div class="bg-card rounded-card shadow-card p-6">
        <h2 class="text-lg font-semibold mb-2">Import Data Handy Talky dari Excel</h2>
        <p class="text-sm text-text-secondary mb-4">
            Format kolom: NO, SN/IMEI, NUP, Keberadaan, Kondisi, Pemanfaatan, Posisi. Data mulai dibaca dari baris ke-6.
        </p>

        @if ($resultMessage)
            <div class="mb-4 bg-green-50 text-success text-sm px-4 py-3 rounded-control">
                {{ $resultMessage }}
            </div>
        @endif
        @if (count($skippedDetails) > 0)
            <div class="mb-4 bg-amber-50 text-warning text-xs px-4 py-3 rounded-control max-h-48 overflow-y-auto">
        <p class="font-semibold mb-1">Detail baris yang dilewati:</p>
        @foreach ($skippedDetails as $detail)
            <p>{{ $detail }}</p>
        @endforeach
    </div>
@endif

        <form wire:submit="import">
            <input type="file" wire:model="file" accept=".xlsx,.xls" class="block w-full text-sm border border-border rounded-control p-2">
            <x-input-error :messages="$errors->get('file')" class="mt-2" />

            <div wire:loading wire:target="file" class="text-sm text-text-secondary mt-2">Mengunggah file...</div>

            <button type="submit" wire:loading.attr="disabled" wire:target="import"
                class="mt-4 bg-primary text-white text-sm px-4 py-2 rounded-control hover:bg-primary-dark disabled:opacity-50">
                <span wire:loading.remove wire:target="import">Import Sekarang</span>
                <span wire:loading wire:target="import">Memproses...</span>
            </button>
        </form>
    </div>
</div>