<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Monitoring Keterlambatan') }}</h2>
    </x-slot>

    <livewire:late-return.index />
</x-app-layout>