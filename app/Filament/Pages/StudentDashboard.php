<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class StudentDashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Avaliações';

    protected static ?string $title = 'Avaliações';
}
