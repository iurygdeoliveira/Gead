<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Professor')
                    ->components([
                        TextEntry::make('name')
                            ->label('Nome Completo'),
                        TextEntry::make('email')
                            ->label('E-mail'),
                        TextEntry::make('team.name')
                            ->label('Campus Vinculado'),
                    ]),
            ]);
    }
}
