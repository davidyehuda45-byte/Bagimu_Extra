<?php

namespace App\Models;

use App\Enums\IzinJenis;
use App\Enums\IzinStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Izin extends Model
{
    protected $table = 'izin';

    protected $fillable = [
        'registration_id',
        'eskul_id',
        'tanggal',
        'jenis',
        'alasan',
        'bukti',
        'status',
        'decided_by',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jenis' => IzinJenis::class,
            'status' => IzinStatus::class,
            'decided_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }
}
