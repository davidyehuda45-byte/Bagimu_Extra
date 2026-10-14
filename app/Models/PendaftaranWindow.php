<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftaranWindow extends Model
{
    protected $primaryKey = 'periode';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'periode',
        'buka',
        'tgl_buka',
        'tgl_tutup',
    ];

    protected function casts(): array
    {
        return [
            'buka' => 'boolean',
            'tgl_buka' => 'datetime',
            'tgl_tutup' => 'datetime',
        ];
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'periode', 'periode');
    }

    /**
     * Apakah pendaftaran sedang menerima pendaftar baru.
     */
    public function isOpen(): bool
    {
        if (! $this->buka) {
            return false;
        }

        $now = now();

        if ($this->tgl_buka && $now->lt($this->tgl_buka)) {
            return false;
        }

        if ($this->tgl_tutup && ! $now->lt($this->tgl_tutup)) {
            return false;
        }

        return true;
    }

    /**
     * Label status jendela untuk formulir.
     */
    public function label(): string
    {
        if (! $this->buka) {
            return 'Pendaftaran ditutup.';
        }

        $now = now();

        if ($this->tgl_buka && $now->lt($this->tgl_buka)) {
            return 'Dibuka mulai '.$this->tgl_buka->format('d M Y H:i').'.';
        }

        if ($this->tgl_tutup && ! $now->lt($this->tgl_tutup)) {
            return 'Pendaftaran telah ditutup '.$this->tgl_tutup->format('d M Y H:i').'.';
        }

        if ($this->tgl_tutup) {
            return 'Dibuka hingga '.$this->tgl_tutup->format('d M Y H:i').'.';
        }

        return 'Pendaftaran dibuka.';
    }
}
