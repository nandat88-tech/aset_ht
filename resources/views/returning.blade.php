<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pengembalian Handy Talky') }}</h2>
    </x-slot>

    @if (session('message'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 text-success text-sm px-4 py-3 rounded-control">
                {{ session('message') }}
            </div>
        </div>
    @endif

    <livewire:returning.index />
</x-app-layout>