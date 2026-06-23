<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'email',
    'token',
    'expires_at',
])]
class MagicLoginToken extends Model
{
    /** @return array<string, string> */
    #[\Override]
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    protected function scopeValid(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }
}
