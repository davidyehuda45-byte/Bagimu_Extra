<?php

namespace App\Models;

use App\Enums\ActivityJenis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityPlan extends Model
{
    protected $fillable = [
        'eskul_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'jenis',
        'agenda',
        'tujuan',
        'materi',
        'lokasi',
        'dokumentasi',
        'hasil',
        'kendala',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jenis' => ActivityJenis::class,
        ];
    }

    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }
}
