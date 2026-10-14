<?php

namespace App\Models;

use App\Enums\LoginMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    protected $table = 'login_history';

    protected $fillable = [
        'user_id',
        'email',
        'ip_hash',
        'method',
        'success',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'method' => LoginMethod::class,
            'success' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
