<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\DeleteStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use App\Traits\Filament\HasConfigurableNavigationSort;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    use HasConfigurableNavigationSort;

    protected static ?string $model = Student::class;

    protected static ?string $tenantOwnershipRelationshipName = 'team';

    protected static string|BackedEnum|null $navigationIcon = 'icon-student';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Alunos';

    protected static ?string $title = 'Alunos';

    protected static ?int $navigationSort = 2;

    #[\Override]
    public static function getModelLabel(): string
    {
        return __('Aluno');
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    #[\Override]
    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
            'delete' => DeleteStudent::route('/{record}/delete'),
        ];
    }
}
