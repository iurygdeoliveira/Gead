<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Curso')
                    ->components([
                        TextEntry::make('name')
                            ->label('Nome'),
                        TextEntry::make('code')
                            ->label('Código'),
                        TextEntry::make('team.name')
                            ->label('Campus Vinculado'),
                    ]),
            ]);
    }
}
