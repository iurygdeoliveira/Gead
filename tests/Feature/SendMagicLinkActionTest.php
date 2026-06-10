<?php

use App\Models\User;
use App\Actions\Auth\SendMagicLinkAction;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
});

it('prevents admins from requesting magic link', function () {
    $admin = User::factory()->create();
    Role::findOrCreate('admin', 'web'); // Ensure role exists
    $admin->assignRole('admin');
    
    expect(fn() => app(SendMagicLinkAction::class)->execute($admin->email))
        ->toThrow(ValidationException::class);
});

it('dispatches email and generates hashed token for valid user', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'aluno@estudante.ifto.edu.br']);
    
    app(SendMagicLinkAction::class)->execute($user->email);
    
    expect(\App\Models\MagicLoginToken::count())->toBe(1);
    Notification::assertSentTo($user, MagicLinkNotification::class);
});
