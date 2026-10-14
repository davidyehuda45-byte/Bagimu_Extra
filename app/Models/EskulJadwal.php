<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EskulJadwal extends Model
{
    protected $fillable = [
        'eskul_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'tempat',
    ];

    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }
}
