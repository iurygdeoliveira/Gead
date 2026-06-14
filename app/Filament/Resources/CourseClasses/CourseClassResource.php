<?php

namespace App\Filament\Resources\CourseClasses;

use App\Filament\Resources\CourseClasses\Pages;
use App\Filament\Resources\CourseClasses\Schemas\CourseClassForm;
use App\Filament\Resources\CourseClasses\Schemas\CourseClassInfolist;
use App\Filament\Resources\CourseClasses\Tables\CourseClassesTable;
use App\Models\CourseClass;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CourseClassResource extends Resource
{
    protected static ?string $model = CourseClass::class;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Turmas';

    protected static ?string $title = 'Turmas';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('Turma');
    }

    public static function form(Schema $schema): Schema
    {
        return CourseClassForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseClassInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseClassesTable::configure($table);
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
            'index' => Pages\ListCourseClasses::route('/'),
            'create' => Pages\CreateCourseClass::route('/create'),
            'view' => Pages\ViewCourseClass::route('/{record}'),
            'edit' => Pages\EditCourseClass::route('/{record}/edit'),
        ];
    }
}
