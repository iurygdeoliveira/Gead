<div>
    <x-filament::button wire:click="generate" color="primary" icon="heroicon-m-cog-6-tooth" wire:target="generate" wire:loading.attr="disabled" x-bind:disabled="!$wire.period" class="w-full sm:w-auto">
        Gerar Avaliações
    </x-filament::button>
</div>
