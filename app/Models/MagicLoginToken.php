<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MagicLoginToken extends Model
{
    protected $fillable = [
        'email',
        'token',
        'expires_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }
}
