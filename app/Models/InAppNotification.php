<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InAppNotification extends Model
{
    protected $table = 'inapp_notifications';

    protected $fillable = [
        'user_id',
        'judul',
        'isi',
        'dibaca_at',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if ($this->dibaca_at === null) {
            $this->update(['dibaca_at' => now()]);
        }
    }
}
