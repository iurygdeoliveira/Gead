<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Curso')
                    ->description('Informações básicas do curso.')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->maxLength(255)
                            ->unique(),
                    ]),
            ]);
    }
}
