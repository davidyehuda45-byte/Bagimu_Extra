<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    protected $fillable = [
        'eskul_id',
        'judul',
        'tingkat',
        'tanggal',
        'peserta',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }
}
