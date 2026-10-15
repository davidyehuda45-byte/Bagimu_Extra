<?php

namespace App\Support;

use App\Models\User;

/**
 * Token login 8 digit (ala e-voting): kredensial cetak untuk siswa.
 * Hash (HMAC-SHA256 + app key) dipakai untuk verifikasi; plaintext
 * juga disimpan di kolom login_token agar kartu bisa dicetak ulang.
 * Plaintext ditampilkan sekali saat diterbitkan dan wajib disampaikan
 * langsung ke siswa karena tidak ada kanal email untuk akun NIS.
 */
class LoginToken
{
    public static function generate(): string
    {
        return (string) random_int(10000000, 99999999);
    }

    public static function hash(string $token): string
    {
        return hash_hmac('sha256', $token, config('app.key'));
    }

    public static function verify(string $plain, string $hash): bool
    {
        return hash_equals($hash, self::hash($plain));
    }

    /**
     * Terbitkan token baru untuk user. Mengembalikan plaintext.
     * Plaintext disimpan di login_token untuk pencetakan ulang kartu;
     * token lama hangus saat reset. Sampaikan langsung ke siswa.
     */
    public static function issue(User $user): string
    {
        $token = self::generate();

        $user->forceFill([
            'login_token' => $token,
            'login_token_hash' => self::hash($token),
            'login_token_created_at' => now(),
            'login_token_revoked_at' => null,
        ])->save();

        return $token;
    }

    public static function revoke(User $user): void
    {
        $user->forceFill(['login_token_revoked_at' => now()])->save();
    }
}
