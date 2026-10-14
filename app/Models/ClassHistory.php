<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassHistory extends Model
{
    protected $table = 'class_history';

    protected $fillable = [
        'user_id',
        'registration_id',
        'periode',
        'kelas_lama',
        'kelas_baru',
        'actor_id',
        'reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
