<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\Jurusan;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    protected $fillable = [
        'user_id',
        'periode',
        'kelas',
        'no_absen',
        'nama_wali_kelas',
        'nis',
        'jurusan',
        'usia',
        'gender',
        'whatsapp',
        'eskul_utama',
        'eskul_cadangan',
        'alasan',
        'alasan_penolakan',
        'status',
        'nilai',
        'predikat',
        'catatan_nilai',
        'verify_token',
        'card_revoked_at',
        'card_version',
    ];

    protected function casts(): array
    {
        return [
            'jurusan' => Jurusan::class,
            'gender' => Gender::class,
            'status' => Status::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }

    public function nilaiHistory(): HasMany
    {
        return $this->hasMany(NilaiHistory::class);
    }

    public function izin(): HasMany
    {
        return $this->hasMany(Izin::class);
    }

    public function classHistory(): HasMany
    {
        return $this->hasMany(ClassHistory::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    /**
     * Kode pendaftaran ala ESK-XXXXXXXXXX (10 char, stabil dari ID).
     */
    public function kode(): string
    {
        return 'ESK-'.strtoupper(str_pad(base_convert($this->getKey(), 10, 36), 10, '0', STR_PAD_LEFT));
    }

    public static function dariKode(string $kode): ?self
    {
        if (! preg_match('/^ESK-([0-9A-Z]{1,10})$/i', trim($kode), $m)) {
            return null;
        }

        $id = (int) base_convert(strtoupper($m[1]), 36, 10);

        return $id > 0 ? self::find($id) : null;
    }
}
