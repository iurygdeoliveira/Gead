<?php

declare(strict_types=1);

namespace App\Filament\Resources\Feedback;

use App\Enums\RoleType;
use App\Filament\Resources\Feedback\Pages\DeleteFeedback;
use App\Filament\Resources\Feedback\Pages\ListFeedbacks;
use App\Filament\Resources\Feedback\Pages\ViewFeedback;
use App\Models\Feedback;
use App\Models\User;
use App\Traits\Filament\HasConfigurableNavigationSort;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class FeedbackResource extends Resource
{
    use HasConfigurableNavigationSort;

    protected static ?string $model = Feedback::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $recordTitleAttribute = 'page_title';

    protected static ?string $navigationLabel = 'Feedbacks';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $title = 'Feedbacks';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        return $user?->hasRole(RoleType::ADMIN->value) ?? false;
    }

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Feedback');
    }

    #[\Override]
    public static function getPluralModelLabel(): string
    {
        return __('Feedbacks');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('user.name')
                    ->label('Usuário'),
                TextInput::make('page_title')
                    ->label('Título da Página'),
                TextInput::make('page_url')
                    ->label('URL'),
                Textarea::make('message')
                    ->label('Mensagem')
                    ->columnSpanFull(),
            ]);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Usuário'),
                TextEntry::make('page_title')
                    ->label('Título da Página'),
                TextEntry::make('page_url')
                    ->label('URL'),
                TextEntry::make('message')
                    ->label('Mensagem')
                    ->columnSpanFull(),
            ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable(isGlobal: false, isIndividual: true),
                TextColumn::make('page_title')
                    ->label('Página')
                    ->searchable(isGlobal: false, isIndividual: true),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Data'),
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListFeedbacks::route('/'),
            'view' => ViewFeedback::route('/{record}'),
            'delete' => DeleteFeedback::route('/{record}/delete'),
        ];
    }
}
