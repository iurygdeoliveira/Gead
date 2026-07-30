<?php

declare(strict_types=1);

namespace App\Filament\Resources\Security\Pages;

use App\Filament\Resources\Security\SecurityEventResource;
use App\Traits\Filament\HasFeedbackAction;
use WallaceMartinss\FilamentSecurity\Filament\Resources\SecurityEventResource\Pages\ListSecurityEvents as BaseListSecurityEvents;

class ListSecurityEvents extends BaseListSecurityEvents
{
    use HasFeedbackAction;

    protected static string $resource = SecurityEventResource::class;
}
