<?php

namespace App\Filament\Resources\Students\Actions;

use App\Enums\RoleType;
use App\Models\Student;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class ChangeStudentAccessStatusBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('changeAccessStatus')
            ->label('Alterar Acesso')
            ->icon(Heroicon::ShieldExclamation)
            ->schema([
                Select::make('status')
                    ->label('Novo Status de Acesso')
                    ->options([
                        'suspend' => 'Suspender Acesso',
                        'release' => 'Liberar Acesso',
                    ])
                    ->required(),
            ])
            ->action(function (Collection $records, array $data): void {
                $isSuspended = $data['status'] === 'suspend';
                foreach ($records as $record) {
                    /** @var Student $record */
                    if ($record->user) {
                        $record->user->update(['is_suspended' => $isSuspended]);
                    }
                }
            })
            ->deselectRecordsAfterCompletion()
            ->visible(fn (): bool => self::canManageAccess());
    }

    private static function canManageAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        /** @var Team|null $team */
        $team = Filament::getTenant();

        if (! $team) {
            return false;
        }

        return $user->getRolesForTeam($team)
            ->whereIn('name', [RoleType::MANAGER->value, RoleType::TAE->value])
            ->isNotEmpty();
    }
}
