<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes do Aluno')
                    ->components([
                        TextEntry::make('registration_number')
                            ->label('Matrícula'),
                        TextEntry::make('name')
                            ->label('Nome Completo'),
                        TextEntry::make('email')
                            ->label('E-mail Institucional'),
                        TextEntry::make('course.name')
                            ->label('Curso'),
                        TextEntry::make('team.name')
                            ->label('Campus Vinculado'),
                    ]),
            ]);
    }
}
