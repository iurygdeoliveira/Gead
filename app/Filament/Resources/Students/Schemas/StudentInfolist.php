<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Tabs')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Detalhes do Aluno')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nome Completo'),
                                TextEntry::make('email')
                                    ->label('E-mail Institucional'),
                                TextEntry::make('team.name')
                                    ->label('Campus Vinculado'),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Avaliações Previstas')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                \Filament\Infolists\Components\RepeatableEntry::make('evaluations_status')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('discipline_name')
                                            ->label('Disciplina'),
                                        TextEntry::make('teacher_name')
                                            ->label('Professor'),
                                        TextEntry::make('teaching_period')
                                            ->label('Período Letivo'),
                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'Realizada' => 'success',
                                                'Pendente' => 'danger',
                                                default => 'gray',
                                            }),
                                    ])
                                    ->columns(4),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
