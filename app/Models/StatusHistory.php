<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusHistory extends Model
{
    protected $table = 'status_history';

    protected $fillable = [
        'registration_id',
        'old_status',
        'new_status',
        'actor_id',
        'actor_email',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'old_status' => Status::class,
            'new_status' => Status::class,
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
