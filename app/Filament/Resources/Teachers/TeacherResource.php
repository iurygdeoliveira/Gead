<?php

namespace App\Filament\Resources\Teachers;

use App\Filament\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Resources\Teachers\Pages\ListTeachers;
use App\Filament\Resources\Teachers\Schemas\TeacherForm;
use App\Filament\Resources\Teachers\Tables\TeachersTable;
use App\Models\Teacher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $tenantOwnershipRelationship = 'team';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Professores';

    protected static ?string $title = 'Professores';

    protected static ?int $navigationSort = 2;

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Professor');
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return TeacherForm::configure($schema);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return \App\Filament\Resources\Teachers\Schemas\TeacherInfolist::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return TeachersTable::configure($table);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListTeachers::route('/'),
            'create' => CreateTeacher::route('/create'),
            'view' => \App\Filament\Resources\Teachers\Pages\ViewTeacher::route('/{record}'),
            'edit' => EditTeacher::route('/{record}/edit'),
            'delete' => \App\Filament\Resources\Teachers\Pages\DeleteTeacher::route('/{record}/delete'),
        ];
    }
}
