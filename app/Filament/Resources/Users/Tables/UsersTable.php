<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleType;
use App\Events\UserApproved;
use App\Filament\Resources\Users\Actions\DeleteUserAction;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        $currentUser = Filament::auth()->user();
        $isAdmin = false;

        if ($currentUser instanceof User) {
            $isAdmin = $currentUser->hasRole(RoleType::ADMIN->value);
        }

        $query = User::query()->withoutRole(RoleType::ADMIN->value)
            ->with(['student.enrollments.course', 'student.enrollments.classEnrollments.courseClass']);
        $currentTeam = Filament::getTenant();

        if (! $isAdmin) {
            if ($currentTeam instanceof Team) {
                $query->whereHas('teams', function (Builder $q) use ($currentTeam): void {
                    $q->where('teams.id', $currentTeam->getKey());
                })->with('teams')->withRolesForTeam($currentTeam);
            }
        } else {
            $query->with(['teams', 'rolesWithTeams']);
        }

        return $table
            ->query($query)
            ->columns([
                self::getNameColumn(),
                self::getEmailColumn(),
                self::getCourseColumn(),
                self::getClassColumn(),
                self::getStatusColumn(),
                self::getApprovalColumn(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon(Heroicon::Eye)
                        ->color('secondary'),
                    EditAction::make()
                        ->icon(Heroicon::Pencil)
                        ->visible(fn (User $record): bool => Filament::auth()->user()->can('update', $record) && $record->isApproved()),
                    DeleteUserAction::make()->icon(Heroicon::Trash),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'desc');
    }

    /**
     * Coluna do nome do usuário
     */
    private static function getNameColumn(): Column
    {
        return TextColumn::make('name')
            ->searchable(isIndividual: true, isGlobal: false)
            ->sortable();
    }

    /**
     * Coluna do email do usuário
     */
    private static function getEmailColumn(): Column
    {
        return TextColumn::make('email')
            ->label('Email address');
    }

    /**
     * Coluna de curso (para alunos)
     */
    private static function getCourseColumn(): Column
    {
        return TextColumn::make('cursos')
            ->label('Curso')
            ->getStateUsing(function (User $record): array|string {
                if (! $record->student) {
                    return '—';
                }

                $courses = $record->student->enrollments
                    ->map(fn ($enrollment) => $enrollment->course?->name)
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                return empty($courses) ? '—' : $courses;
            })
            ->listWithLineBreaks()
            ->bulleted(fn ($state): bool => is_array($state) && count($state) > 1);
    }

    /**
     * Coluna de turma (para alunos)
     */
    private static function getClassColumn(): Column
    {
        return TextColumn::make('turmas')
            ->label('Turma')
            ->getStateUsing(function (User $record): array|string {
                if (! $record->student) {
                    return '—';
                }

                $classes = collect();
                foreach ($record->student->enrollments as $enrollment) {
                    foreach ($enrollment->classEnrollments as $classEnrollment) {
                        if ($classEnrollment->courseClass) {
                            $classes->push($classEnrollment->courseClass->name);
                        }
                    }
                }

                $classes = $classes->filter()->unique()->values()->toArray();

                return empty($classes) ? '—' : $classes;
            })
            ->listWithLineBreaks()
            ->bulleted(fn ($state): bool => is_array($state) && count($state) > 1);
    }

    /**
     * Coluna de status (suspenso/autorizado) - apenas para usuários aprovados
     */
    private static function getStatusColumn(): Column
    {
        return TextColumn::make('is_suspended')
            ->label('Acesso')
            ->sortable()
            ->formatStateUsing(fn (User $record): string => $record->is_suspended ? __('Suspenso') : __('Liberado'))
            ->badge()
            ->color(fn (User $record): string => $record->is_suspended ? 'danger' : 'primary')
            ->alignCenter();
    }

    /**
     * Coluna de aprovação - apenas para usuários não aprovados e visível para Admin/Owner
     */
    private static function getApprovalColumn(): ToggleColumn
    {
        return ToggleColumn::make('is_approved')
            ->onColor('primary')
            ->offColor('danger')
            ->onIcon(Heroicon::Check)
            ->offIcon(Heroicon::XMark)
            ->label('Aprovar')
            ->disabled(fn (User $record): bool => ! Filament::auth()->user()?->can('update', $record))
            ->afterStateUpdated(function (User $record, $state): void {
                // Se o usuário foi aprovado
                if ($state) {
                    // Remover suspensão
                    $record->is_suspended = false;

                    // Se o email não está verificado, verificar automaticamente
                    if (! $record->hasVerifiedEmail()) {
                        $record->markEmailAsVerified();
                    }

                    $record->save();

                    // Disparar evento de aprovação
                    event(new UserApproved($record));
                }
            });
    }

    // Removidos os helpers getTenantRolesForUser, etc.
}
