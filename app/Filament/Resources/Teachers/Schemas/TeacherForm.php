<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Professor')
                    ->description('Informações básicas do professor.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->components(self::getPersonalDataFields()),
            ]);
    }

    public static function getPersonalDataFields(): array
    {
        return [
            TextInput::make('name')
                ->label('Nome Completo')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(modifyRuleUsing: fn (Unique $rule) => $rule->where('team_id', Filament::getTenant()?->getKey())),
            TextInput::make('registration_number')
                ->label('Matrícula')
                ->maxLength(255),
        ];
    }
}
