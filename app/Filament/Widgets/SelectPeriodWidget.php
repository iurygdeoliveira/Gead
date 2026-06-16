<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class SelectPeriodWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.widgets.select-period-widget';
    protected int | string | array $columnSpan = 1;

    public ?string $period = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('period')
                    ->label('Período Letivo')
                    ->options([
                        '2025.2' => '2025.2',
                        '2026.1' => '2026.1',
                    ])
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->dispatch('period-updated', period: $state))
                    ->required(),
            ]);
    }

    public static function canView(): bool
    {
        return filament()->getCurrentPanel()?->getId() === 'manager';
    }
}
