<x-filament-panels::page>
    <form wire:submit="updatePassword">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit">
                Alterar Senha
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
