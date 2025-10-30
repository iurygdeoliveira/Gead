<?php

namespace App\Filament\Resources\Media;

use App\Filament\Resources\Media\Pages\CreateMedia;
use App\Filament\Resources\Media\Pages\DeleteMedia;
use App\Filament\Resources\Media\Pages\EditMedia;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Filament\Resources\Media\Pages\ViewMedia;
use App\Filament\Resources\Media\Schemas\MediaForm;
use App\Filament\Resources\Media\Schemas\MediaInfolist;
use App\Filament\Resources\Media\Tables\MediaTable;
use App\Models\MediaItem;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Override;

class MediaResource extends Resource
{
    protected static ?string $model = MediaItem::class;

    // Define o relacionamento de pertença ao tenant para este Resource
    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    protected static ?string $navigationLabel = 'Mídias';

    protected static ?string $title = 'Mídias';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    /**
     * Verifica se o usuário pode acessar o recurso de mídia.
     * Configura o contexto do tenant antes de verificar a Policy,
     * garantindo que as permissões sejam verificadas no contexto correto.
     * Se o tenant não estiver configurado ainda (durante construção da navegação),
     * verifica se o usuário tem acesso a pelo menos um tenant com permissões de mídia.
     */
    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        return Gate::forUser($user)->allows('viewAny', MediaItem::class);
    }

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Media');
    }

    public static function form(Schema $schema): Schema
    {
        return MediaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MediaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaTable::configure($table)
            ->modifyQueryUsing(function ($query) {
                return $query->with('video');
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedia::route('/'),
            'create' => CreateMedia::route('/create'),
            'view' => ViewMedia::route('/{record}'),
            'edit' => EditMedia::route('/{record}/edit'),
            'delete' => DeleteMedia::route('/{record}/delete'),
        ];
    }

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        if ((bool) $record->video) {
            $title = $record->video?->title;

            return $title ?: 'Vídeo (URL)';
        }

        return $record->getFirstMedia('media')?->name ?? 'Sem nome';
    }
}
