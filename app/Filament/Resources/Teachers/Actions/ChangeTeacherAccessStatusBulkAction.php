<?php

namespace App\Filament\Resources\Teachers\Actions;

use App\Enums\RoleType;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class ChangeTeacherAccessStatusBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('changeAccessStatus')
            ->label('Alterar Acesso')
            ->icon(Heroicon::ShieldExclamation)
            ->form([
                Select::make('status')
                    ->label('Novo Status de Acesso')
                    ->options([
                        'suspend' => 'Suspender Acesso',
                        'release' => 'Liberar Acesso',
                    ])
                    ->required(),
            ])
            ->action(function (Collection $records, array $data) {
                $isSuspended = $data['status'] === 'suspend';
                foreach ($records as $record) {
                    if ($record->user) {
                        $record->user->update(['is_suspended' => $isSuspended]);
                    }
                }
            })
            ->deselectRecordsAfterCompletion()
            ->visible(fn () => self::canManageAccess());
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
        
        $team = Filament::getTenant();
        
        if (! $team) {
            return false;
        }
        
        return $user->getRolesForTeam($team)
            ->whereIn('name', [RoleType::MANAGER->value, RoleType::TAE->value])
            ->isNotEmpty();
    }
}
