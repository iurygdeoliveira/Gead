<?php

declare(strict_types=1);

namespace App\Filament\Configurators;

use Filament\Forms\Components\Field;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Facades\FilamentColor;

class FilamentComponentsConfigurator
{
    public static function configure(): void
    {
        FilamentColor::register([
            'danger' => [
                50 => '#fef2f2',
                100 => '#fee2e2',
                200 => '#fecaca',
                300 => '#fca5a5',
                400 => '#f87171',
                500 => '#e7010a',
                600 => '#e7010a',
                700 => '#b91c1c',
                800 => '#991b1b',
                900 => '#7f1d1d',
                950 => '#450a0a',
            ],
        ]);

        Field::configureUsing(function (Field $field): void {
            $field->translateLabel();
        });

        Column::configureUsing(function (Column $column): void {
            $column->translateLabel();
        });

        IconColumn::configureUsing(function (IconColumn $iconColumn): void {
            $iconColumn
                ->alignment(Alignment::Center)
                ->verticalAlignment(VerticalAlignment::Center);
        });

        TextColumn::configureUsing(function (TextColumn $textColumn): void {
            $textColumn->wrap();
        });

        CheckboxColumn::configureUsing(function (CheckboxColumn $checkboxColumn): void {
            $checkboxColumn
                ->alignment(Alignment::Center)
                ->verticalAlignment(VerticalAlignment::Center);
        });

        Table::configureUsing(function (Table $table): void {
            $table
                ->deferLoading()
                ->persistSortInSession()
                ->persistSearchInSession()
                ->extremePaginationLinks()
                ->defaultPaginationPageOption(20)
                ->paginated([20, 40, 60, 80, 'all'])
                ->emptyStateIcon(Heroicon::ExclamationTriangle);
        });
    }
}
