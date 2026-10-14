<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = [
        'email',
        'code_hash',
        'expires_at',
        'attempts',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return now()->gt($this->expires_at);
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }
}
