<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Dados do Curso')
                    ->description('Informações básicas do curso.')
                    ->columns(2)
                    ->components([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ]),
            ]);
    }
}
