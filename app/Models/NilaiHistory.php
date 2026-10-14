<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiHistory extends Model
{
    protected $table = 'nilai_history';

    protected $fillable = [
        'registration_id',
        'nilai',
        'predikat',
        'catatan',
        'actor_id',
        'actor_email',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
