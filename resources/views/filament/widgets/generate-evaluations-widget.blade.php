<x-filament-widgets::widget>
    <x-filament::section class="h-full flex items-center justify-center pt-8">
        <x-filament::button wire:click="generate" color="primary" icon="heroicon-m-cog-6-tooth" wire:target="generate" wire:loading.attr="disabled" x-bind:disabled="!$wire.period" class="w-full">
            Gerar Avaliações
        </x-filament::button>
    </x-filament::section>
</x-filament-widgets::widget>
