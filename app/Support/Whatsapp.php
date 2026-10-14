<?php

namespace App\Support;

class Whatsapp
{
    /**
     * Normalisasi nomor HP Indonesia ke format internasional tanpa '+'.
     * 08… → 628…, spasi/titik/strip/() dibuang.
     */
    public static function normalize(?string $no): ?string
    {
        if ($no === null || trim($no) === '') {
            return null;
        }

        $n = preg_replace('/[\s\-.()]/', '', trim($no));

        if (str_starts_with($n, '+')) {
            $n = substr($n, 1);
        }

        if (str_starts_with($n, '08')) {
            $n = '62'.substr($n, 1);
        }

        return $n;
    }

    public static function link(?string $no): ?string
    {
        $n = self::normalize($no);

        return $n ? 'https://wa.me/'.$n : null;
    }
}
