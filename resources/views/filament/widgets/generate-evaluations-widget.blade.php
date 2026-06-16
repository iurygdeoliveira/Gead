<x-filament-widgets::widget style="height: 100%;">
    <x-filament::section style="height: 100%; display: flex; flex-direction: column; justify-content: center;">
        <div style="display: flex; width: 100%; height: 100%; min-height: 66px; align-items: center; justify-content: center;">
            <x-filament::button wire:click="generate" color="primary" icon="heroicon-m-cog-6-tooth" wire:target="generate" wire:loading.attr="disabled" x-bind:disabled="!$wire.period" class="w-full">
                Gerar Avaliações
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
