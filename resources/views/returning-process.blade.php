<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Proses Pengembalian') }}</h2>
    </x-slot>

    <livewire:returning.process :transaction-id="$transactionId" />
</x-app-layout>