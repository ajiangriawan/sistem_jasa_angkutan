<x-filament::page>
    <form wire:submit.prevent="print" class="space-y-6">

        {{-- Form dari Livewire --}}
        {{ $this->form }}

        {{-- Tombol Cetak dan indikator loading --}}
        <div class="flex items-center gap-4">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                Cetak PDF
            </x-filament::button>

            <div wire:loading.delay wire:target="print" class="flex items-center gap-2 text-sm text-gray-600">
                <x-filament::loading-indicator class="h-5 w-5" />
                <span>Memproses PDF...</span>
            </div>
        </div>
    </form>
</x-filament::page>