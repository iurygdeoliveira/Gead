<?php

namespace App\Filament\Resources\Courses;

use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\Pages\ListCourses;
use App\Filament\Resources\Courses\Schemas\CourseForm;
use App\Filament\Resources\Courses\Tables\CoursesTable;
use App\Models\Course;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $tenantOwnershipRelationship = 'team';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Cursos';

    protected static ?string $title = 'Cursos';

    protected static ?int $navigationSort = 2;

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Curso');
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return CourseForm::configure($schema);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return \App\Filament\Resources\Courses\Schemas\CourseInfolist::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return CoursesTable::configure($table);
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
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'view' => \App\Filament\Resources\Courses\Pages\ViewCourse::route('/{record}'),
            'edit' => EditCourse::route('/{record}/edit'),
            'delete' => \App\Filament\Resources\Courses\Pages\DeleteCourse::route('/{record}/delete'),
        ];
    }
}
