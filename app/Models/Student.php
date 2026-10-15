<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\Jurusan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'kelas',
        'jurusan',
        'gender',
        'no_absen',
        'wali_nama',
        'wali_kontak',
        'whatsapp',
        'status',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'jurusan' => Jurusan::class,
            'gender' => Gender::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'AKTIF');
    }
}
