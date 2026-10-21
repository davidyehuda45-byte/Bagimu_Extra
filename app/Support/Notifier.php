<?php

namespace App\Support;

use App\Enums\NotifChannel;
use App\Enums\NotifStatus;
use App\Enums\Role;
use App\Models\InAppNotification;
use App\Models\NotificationLog;
use App\Models\Registration;
use App\Models\User;

class Notifier
{
    public static function kePengguna(int $userId, string $judul, string $isi): void
    {
        InAppNotification::create([
            'user_id' => $userId,
            'judul' => $judul,
            'isi' => $isi,
        ]);
    }

    private static function catat(NotifChannel $channel, string $to, string $template, string $dedupe): void
    {
        NotificationLog::firstOrCreate(
            ['dedupe_key' => $dedupe],
            [
                'channel' => $channel,
                'to' => $to,
                'template' => $template,
                'status' => NotifStatus::TERKIRIM,
                'sent_at' => now(),
            ]
        );
    }

    public static function statusPendaftaran(Registration $registration): void
    {
        // Tanpa alur persetujuan: notifikasi ini konfirmasi pendaftaran tercatat.
        self::pendaftaranTercatat($registration);
    }

    public static function pendaftaranTercatat(Registration $registration): void
    {
        self::kePengguna(
            $registration->user_id,
            'Pendaftaran berhasil',
            "Anda resmi terdaftar eskul {$registration->eskul_utama} periode {$registration->periode}."
        );
        self::catat(
            NotifChannel::INAPP,
            'user:'.$registration->user_id,
            'PENDAFTARAN_MASUK',
            "reg-{$registration->id}"
        );
    }

    public static function pengumumanBaru(string $judul): void
    {
        $now = now();

        $rows = User::where('role', Role::STUDENT)->pluck('id')->map(fn ($id) => [
            'user_id' => $id,
            'judul' => 'Pengumuman baru',
            'isi' => $judul,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            InAppNotification::insert($chunk);
        }

        self::catat(NotifChannel::INAPP, 'semua siswa', 'PENGUMUMAN_BARU', 'pengumuman-'.md5($judul));
    }

    public static function belumDibaca(int $userId): int
    {
        return InAppNotification::where('user_id', $userId)->whereNull('dibaca_at')->count();
    }
}
