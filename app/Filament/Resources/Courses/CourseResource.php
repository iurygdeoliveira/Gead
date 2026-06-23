<?php

namespace App\Filament\Resources\Courses;

use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Filament\Resources\Courses\Pages\DeleteCourse;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\Pages\ListCourses;
use App\Filament\Resources\Courses\Pages\ViewCourse;
use App\Filament\Resources\Courses\Schemas\CourseForm;
use App\Filament\Resources\Courses\Schemas\CourseInfolist;
use App\Filament\Resources\Courses\Tables\CoursesTable;
use App\Models\Course;
use App\Traits\Filament\HasConfigurableNavigationSort;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    use HasConfigurableNavigationSort;

    protected static ?string $model = Course::class;

    protected static ?string $recordRouteKeyName = 'uuid';

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'enrollments.student',
            'enrollments.classEnrollments.courseClass',
            'disciplines.teachers',
        ]);
    }

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    protected static string|BackedEnum|null $navigationIcon = 'icon-courses';

    protected static ?string $recordTitleAttribute = 'name';

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
        return CourseInfolist::configure($schema);
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
            'view' => ViewCourse::route('/{record}'),
            'edit' => EditCourse::route('/{record}/edit'),
            'delete' => DeleteCourse::route('/{record}/delete'),
        ];
    }
}
