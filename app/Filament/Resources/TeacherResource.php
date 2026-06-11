<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages\CreateTeacher;
use App\Filament\Resources\TeacherResource\Pages\EditTeacher;
use App\Filament\Resources\TeacherResource\Pages\ListTeachers;
use App\Filament\Resources\TeacherResource\Schemas\TeacherForm;
use App\Filament\Resources\TeacherResource\Tables\TeachersTable;
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

    public static function form(Schema $schema): Schema
    {
        return TeacherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeachersTable::configure($table);
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
            'index' => ListTeachers::route('/'),
            'create' => CreateTeacher::route('/create'),
            'edit' => EditTeacher::route('/{record}/edit'),
        ];
    }
}
