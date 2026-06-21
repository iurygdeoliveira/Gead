<x-filament-widgets::widget style="height: 100%;">
    <style>
        .students-without-class-count {
            font-size: 2.25rem !important;
            font-weight: 800 !important;
            color: #e7010a !important;
            margin-top: 4px !important;
            line-height: 1 !important;
        }
        .students-without-class-label {
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: #e7010a !important;
            transition: color 75ms !important;
        }
        .dark .students-without-class-label {
            color: #f87171 !important;
        }
        .students-without-class-link:hover .students-without-class-label {
            color: var(--danger-600, #dc2626) !important;
        }
        .dark .students-without-class-link:hover .students-without-class-label {
            color: var(--danger-400, #f87171) !important;
        }
    </style>
    <x-filament::section style="height: 100%; display: flex; flex-direction: column; justify-content: center;">
        <a href="{{ \App\Filament\Resources\Students\StudentResource::getUrl('index', ['activeTab' => 'sem_turma']) }}" style="display: flex; flex-direction: column; width: 100%; height: 100%; min-height: 66px; align-items: center; justify-content: center; text-align: center; text-decoration: none;" class="students-without-class-link">
            <span class="students-without-class-label">
                Alunos sem Turma Vinculada
            </span>
            <span class="students-without-class-count">
                {{ $this->getCount() }}
            </span>
        </a>
    </x-filament::section>
</x-filament-widgets::widget>
