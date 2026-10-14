<?php

namespace App\Models;

use App\Enums\PeriodeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Periode extends Model
{
    protected $primaryKey = 'periode';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'periode',
        'nama',
        'tahun_ajaran',
        'semester',
        'tgl_mulai',
        'tgl_selesai',
        'status',
        'max_eskul_per_siswa',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'datetime',
            'tgl_selesai' => 'datetime',
            'status' => PeriodeStatus::class,
        ];
    }

    public function window(): HasOne
    {
        return $this->hasOne(PendaftaranWindow::class, 'periode', 'periode');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'periode', 'periode');
    }
}
