<?php

namespace App\Support;

class Kelas
{
    private const ROMAWI = ['X' => '10', 'XI' => '11', 'XII' => '12'];

    /**
     * Normalisasi "XI Kuliner 1" → "11 KULINER 1" (tingkat angka + jurusan kapital).
     */
    public static function normalisasi(?string $kelas): ?string
    {
        if ($kelas === null) {
            return null;
        }

        $k = trim((string) preg_replace('/\s+/', ' ', $kelas));

        if (preg_match('/^(XII|XI|X|10|11|12)\b\s*(.*)$/i', $k, $m)) {
            $t = strtoupper($m[1]);
            $t = self::ROMAWI[$t] ?? $t;
            $sisa = trim($m[2]);
            // Kapitalkan jurusan bila cocok pola "JURUSAN [nomor]".
            if (preg_match('/^([a-zA-Z]+)(\s+\d{0,2})?$/', $sisa, $j)) {
                $sisa = strtoupper($j[1]).($j[2] ?? '');
            }

            return trim($t.' '.$sisa);
        }

        return $k;
    }

    /**
     * Ambil angka tingkat dari format kelas "10 RPL 1" / "XI Kuliner 1" → 10/11.
     */
    public static function tingkat(?string $kelas): ?int
    {
        $k = self::normalisasi($kelas);

        if (! $k || ! preg_match('/^(\d{1,2})\b/', $k, $m)) {
            return null;
        }

        return (int) $m[1];
    }

    /**
     * Naikkan "10 RPL 1" → "11 RPL 1". Kembalikan null bila tak berpola.
     */
    public static function naikkan(string $kelas): ?string
    {
        $k = self::normalisasi($kelas);

        if (! $k || ! preg_match('/^(\d{1,2})(\s+.*)$/', $k, $m)) {
            return null;
        }

        return ((int) $m[1] + 1).$m[2];
    }
}
