<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pertemuan extends Model
{
    protected $fillable = [
        'eskul_id',
        'nomor',
        'tanggal',
        'keterangan',
        'locked_at',
        'locked_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'locked_at' => 'datetime',
        ];
    }

    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function label(): string
    {
        return 'Pertemuan '.$this->nomor.' • '.$this->tanggal->format('d M Y');
    }
}
