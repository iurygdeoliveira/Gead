<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AuthPanelProvider;
use App\Providers\Filament\ManagerPanelProvider;
use App\Providers\Filament\StudentPanelProvider;
use App\Providers\Filament\TaePanelProvider;
use App\Providers\Filament\TeacherPanelProvider;

return [
    AppServiceProvider::class,
    AuthPanelProvider::class,
    AdminPanelProvider::class,
    ManagerPanelProvider::class,
    TaePanelProvider::class,
    TeacherPanelProvider::class,
    StudentPanelProvider::class,
];
