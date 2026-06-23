<?php

namespace App\Filament\Resources\Teachers\Actions;

use App\Enums\RoleType;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;

class ToggleTeacherSuspensionAction
{
    public static function make(): Action
    {
        return Action::make('toggleSuspension')
            ->label(fn ($record): string => $record->user && $record->user->is_suspended ? 'Liberar Acesso' : 'Suspender Acesso')
            ->icon(fn ($record): Heroicon => $record->user && $record->user->is_suspended ? Heroicon::CheckCircle : Heroicon::NoSymbol)
            ->color(fn ($record): string => $record->user && $record->user->is_suspended ? 'success' : 'danger')
            ->requiresConfirmation()
            ->action(function ($record): void {
                if ($record->user) {
                    $record->user->update(['is_suspended' => ! $record->user->is_suspended]);
                }
            })
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
