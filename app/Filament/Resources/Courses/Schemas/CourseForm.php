<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

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
                            ->unique(modifyRuleUsing: fn (Unique $rule) => $rule->where('team_id', Filament::getTenant()?->getKey())),
                    ]),
            ]);
    }
}
