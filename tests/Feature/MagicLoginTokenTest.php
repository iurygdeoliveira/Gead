<?php

use App\Models\MagicLoginToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters expired tokens using scopeValid', function () {
    MagicLoginToken::create([
        'email' => 'test@ifto.edu.br',
        'token' => 'abc',
        'expires_at' => now()->subMinute(),
    ]);
    
    MagicLoginToken::create([
        'email' => 'test2@ifto.edu.br',
        'token' => 'def',
        'expires_at' => now()->addMinute(),
    ]);
    
    expect(MagicLoginToken::valid()->count())->toBe(1);
});
