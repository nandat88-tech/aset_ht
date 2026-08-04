<?php

use App\Models\BorrowTransaction;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function with(): array
    {
        return [
            'transactions' => BorrowTransaction::with('employee', 'destinationLocation', 'items')
                ->where('status', 'active')
                ->where('loan_type', 'sementara')
                ->whereDate('due_date', '<', now())
                ->orderBy('due_date')
                ->paginate(10),
        ];
    }

    public function sendReminder(int $id): void
    {
        $trx = BorrowTransaction::findOrFail($id);
        $name = ucfirst($trx->employee->name);
        $phone = (string) ($trx->employee->phone ?? '');

        // Normalize phone: remove non-digits and ensure country code (62) for Indonesia
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if ($phone === '') {
            session()->flash('error', 'Nomor telepon peminjam tidak tersedia. Tidak dapat mengirim reminder.');
            return;
        }
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $dueDate = \Carbon\Carbon::parse($trx->due_date)->format('d M Y');
        $message = "Halo $name, ini adalah pengingat bahwa Anda memiliki peminjaman yang sudah melewati tanggal jatuh tempo ($dueDate). Mohon segera mengembalikan barang yang dipinjam. Terima kasih.";

        // Kirim pesan WhatsApp menggunakan API
        $sent = $this->sendWhatsAppMessage($phone, $message);
        
        if ($sent) {
            BorrowTransaction::where('id', $id)->update(['last_reminder_sent_at' => now()]);
            session()->flash('message', 'Reminder tercatat berhasil dikirim.');
        }
    }

    public function sendWhatsAppMessage(string $phone, string $message): bool
    {
        // Implementasi pengiriman pesan WhatsApp menggunakan API
        // Contoh: Menggunakan Guzzle untuk mengirim permintaan ke API WhatsApp
        $client = new \GuzzleHttp\Client();
        $apiUrl = rtrim(env('WHATSAPP_API_URL', ''), '/') . '/api/sendText';
        $apiKey = env('WHATSAPP_API_KEY', '');

        try {
            $headers = ['Content-Type' => 'application/json'];
            if ($apiKey) {
                $headers['X-API-KEY'] = $apiKey;
            }

            $response = $client->post($apiUrl, [
                'headers' => $headers,
                'json' => [
                    'chatId' => $phone,
                    'id' => uniqid('', true),
                    'text' => $message,
                    'session' => 'default',
                ],
                'timeout' => 10,
            ]);

            $status = $response->getStatusCode();
            if ($status >= 200 && $status < 300) {
                return true;
            }
            throw new \Exception('Gagal mengirim pesan WhatsApp. Response status: ' . $status);
        } catch (\Exception $e) {
            // Tangani kesalahan pengiriman pesan
            logger()->error('WhatsApp send error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mengirim pesan WhatsApp.');
            return false;
        }
    }
}; ?>

<div>
    @if (session('message'))
        <div class="mb-4 bg-green-50 text-success text-sm px-4 py-3 rounded-control">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-card rounded-card shadow-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-text-secondary uppercase text-xs">
                <tr>
                    <th class="text-left px-4 py-3">Peminjam</th>
                    <th class="text-left px-4 py-3">Departemen</th>
                    <th class="text-left px-4 py-3">Lokasi Tujuan</th>
                    <th class="text-left px-4 py-3">Tanggal Pinjam</th>
                    <th class="text-left px-4 py-3">Jatuh Tempo</th>
                    <th class="text-left px-4 py-3">Terlambat</th>
                    <th class="text-left px-4 py-3">Reminder Terakhir</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($transactions as $trx)
                    @php $daysLate = (int) \Carbon\Carbon::parse($trx->due_date)->diffInDays(now()); @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $trx->employee->name }}</td>
                        <td class="px-4 py-3">{{ $trx->employee->department }}</td>
                        <td class="px-4 py-3">{{ $trx->destinationLocation->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($trx->due_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="badge badge-danger">{{ $daysLate }} hari</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-text-secondary">
                            {{ $trx->last_reminder_sent_at ? $trx->last_reminder_sent_at->diffForHumans() : 'Belum pernah' }}
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="sendReminder({{ $trx->id }})" class="text-primary hover:underline">
                                Kirim Reminder
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-text-secondary">
                            Tidak ada peminjaman yang terlambat saat ini. 🎉
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
</div>