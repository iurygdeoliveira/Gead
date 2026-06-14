<?php

namespace App\Filament\Resources\CourseClasses\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class CourseClassInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalhes da Turma')
                    ->components([
                        TextEntry::make('name')
                            ->label('Nome da Turma'),
                        TextEntry::make('code')
                            ->label('Código'),
                        TextEntry::make('discipline.name')
                            ->label('Disciplina'),
                        TextEntry::make('teacher.name')
                            ->label('Docente'),
                        TextEntry::make('academicTerm.name')
                            ->label('Período Letivo'),
                    ]),
            ]);
    }
}
