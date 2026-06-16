<?php

use App\Actions\Auth\SendMagicLinkAction;
use App\Models\MagicLoginToken;
use App\Models\User;
use App\Notifications\Auth\MagicLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
});

it('prevents admins from requesting magic link', function () {
    $admin = User::factory()->create();
    Role::findOrCreate('admin', 'web'); // Ensure role exists
    $admin->assignRole('admin');

    expect(fn () => app(SendMagicLinkAction::class)->execute($admin->email))
        ->toThrow(ValidationException::class);
});

it('dispatches email and generates hashed token for valid user', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'aluno@estudante.ifto.edu.br']);

    app(SendMagicLinkAction::class)->execute($user->email);

    expect(MagicLoginToken::count())->toBe(1);
    Notification::assertSentTo($user, MagicLinkNotification::class);
});
