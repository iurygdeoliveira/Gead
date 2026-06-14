<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Dados do Professor')
                    ->description('Informações básicas do professor.')
                    ->columns(2)
                    ->components([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        \Filament\Forms\Components\TextInput::make('registration_number')
                            ->label('Matrícula')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
