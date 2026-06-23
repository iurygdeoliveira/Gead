<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'provider',
    'provider_user_id',
    'name',
    'email',
    'avatar',
    'token',
    'refresh_token',
    'expires_at',
])]
class ConnectedAccount extends Model
{
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
