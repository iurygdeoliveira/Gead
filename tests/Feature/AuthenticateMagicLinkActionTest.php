<?php

use App\Models\User;
use App\Models\Team;
use App\Models\MagicLoginToken;
use App\Actions\Auth\AuthenticateMagicLinkAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
});

it('fails with invalid or expired token', function () {
    expect(fn() => app(AuthenticateMagicLinkAction::class)->execute('invalid-token'))
        ->toThrow(ModelNotFoundException::class);
});

it('authenticates, assigns campus team and activates user', function () {
    Role::findOrCreate('user', 'web');
    $user = User::factory()->create(['email' => 'teste@ifto.edu.br', 'is_approved' => false]);
    $plainToken = Str::random(64);
    
    MagicLoginToken::create([
        'email' => $user->email,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addMinutes(15)
    ]);
    
    $authenticatedUser = app(AuthenticateMagicLinkAction::class)->execute($plainToken);
    
    expect($authenticatedUser->id)->toBe($user->id)
        ->and($authenticatedUser->is_approved)->toBeTrue()
        ->and($authenticatedUser->hasRole('user'))->toBeTrue()
        ->and(Auth::check())->toBeTrue();
        
    $team = Team::where('slug', 'campus-araguaina')->first();
    expect($team)->not->toBeNull()
        ->and($authenticatedUser->teams()->where('teams.id', $team->id)->exists())->toBeTrue();
        
    expect(MagicLoginToken::count())->toBe(0);
});
