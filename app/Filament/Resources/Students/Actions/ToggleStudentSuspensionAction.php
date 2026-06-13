<?php

namespace App\Filament\Resources\Students\Actions;

use App\Enums\RoleType;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;

class ToggleStudentSuspensionAction
{
    public static function make(): Action
    {
        return Action::make('toggleSuspension')
            ->label(fn ($record) => $record->user && $record->user->is_suspended ? 'Liberar Acesso' : 'Suspender Acesso')
            ->icon(fn ($record) => $record->user && $record->user->is_suspended ? Heroicon::CheckCircle : Heroicon::NoSymbol)
            ->color(fn ($record) => $record->user && $record->user->is_suspended ? 'success' : 'danger')
            ->requiresConfirmation()
            ->action(function ($record) {
                if ($record->user) {
                    $record->user->update(['is_suspended' => !$record->user->is_suspended]);
                }
            })
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
