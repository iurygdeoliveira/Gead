<x-filament-widgets::widget style="height: 100%;">
    <x-filament::section style="height: 100%; display: flex; flex-direction: column; justify-content: center;">
        <a href="{{ \App\Filament\Resources\Students\StudentResource::getUrl('index', ['tableFilters' => ['without_class' => ['value' => '1']]]) }}" style="display: flex; width: 100%; height: 100%; min-height: 66px; align-items: center; justify-content: space-between; gap: 1rem; text-decoration: none;" class="group">
            <div style="display: flex; flex-direction: column; justify-content: center;">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition duration-75">
                    Alunos sem Turma Vinculada
                </span>
                <span class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white mt-1">
                    {{ $this->getCount() }}
                </span>
            </div>
            <div class="flex items-center justify-center rounded-full bg-danger-500/10 p-3 text-danger-600 dark:bg-danger-500/20 dark:text-danger-400 group-hover:scale-105 transition-transform duration-75">
                <x-filament::icon
                    icon="heroicon-m-user-minus"
                    class="h-6 w-6"
                />
            </div>
        </a>
    </x-filament::section>
</x-filament-widgets::widget>
