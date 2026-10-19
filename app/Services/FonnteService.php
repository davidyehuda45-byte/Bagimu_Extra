<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public const SETTING_TOKEN = 'whatsapp.fonnte_token';
    public const SETTING_DEVICE = 'whatsapp.fonnte_device';
    public const SETTING_NAME = 'whatsapp.fonnte_name';
    public const SETTING_STATUS = 'whatsapp.fonnte_status';

    /**
     * Validasi token ke endpoint Fonnte dan ambil info device.
     */
    public function validateToken(string $token): array
    {
        $token = trim($token);
        if ($token === '') {
            return [
                'valid' => false,
                'message' => 'Token Fonnte tidak boleh kosong.',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post('https://api.fonnte.com/device');

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] === true) {
                return [
                    'valid' => true,
                    'device' => $data['device'] ?? '—',
                    'name' => $data['name'] ?? 'Device Fonnte',
                    'device_status' => $data['device_status'] ?? ($data['status'] ? 'connect' : 'disconnect'),
                    'package' => $data['package'] ?? '—',
                    'quota' => $data['quota'] ?? 0,
                    'expired' => $data['expired'] ?? '—',
                    'message' => 'Token valid & terhubung ke Fonnte.',
                ];
            }

            $reason = $data['reason'] ?? $data['message'] ?? 'Token tidak valid atau device belum terdaftar.';

            return [
                'valid' => false,
                'message' => 'Gagal validasi Fonnte: '.$reason,
            ];
        } catch (\Throwable $e) {
            Log::warning('Fonnte token validation error: '.$e->getMessage());

            return [
                'valid' => false,
                'message' => 'Tidak dapat menghubungi server Fonnte (koneksi timeout / error).',
            ];
        }
    }

    /**
     * Ambil plain token (didekripsi dari AppSetting).
     */
    public function getToken(): ?string
    {
        $encrypted = AppSetting::get(self::SETTING_TOKEN);
        if (! $encrypted) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (DecryptException $e) {
            Log::error('Fonnte token decryption failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Simpan token secara terenkripsi beserta info device.
     */
    public function saveToken(string $token, array $deviceInfo = []): void
    {
        $token = trim($token);
        AppSetting::set(self::SETTING_TOKEN, Crypt::encryptString($token));

        if (! empty($deviceInfo['device'])) {
            AppSetting::set(self::SETTING_DEVICE, (string) $deviceInfo['device']);
        }
        if (! empty($deviceInfo['name'])) {
            AppSetting::set(self::SETTING_NAME, (string) $deviceInfo['name']);
        }
        if (! empty($deviceInfo['device_status'])) {
            AppSetting::set(self::SETTING_STATUS, (string) $deviceInfo['device_status']);
        }
    }

    /**
     * Putus koneksi / hapus token Fonnte.
     */
    public function disconnect(): void
    {
        AppSetting::set(self::SETTING_TOKEN, '');
        AppSetting::set(self::SETTING_DEVICE, '');
        AppSetting::set(self::SETTING_NAME, '');
        AppSetting::set(self::SETTING_STATUS, '');
    }

    /**
     * Periksa apakah Fonnte sudah terhubung dan ambil summary status untuk UI.
     */
    public function getStatus(): array
    {
        $token = $this->getToken();
        $isConnected = ! empty($token);

        return [
            'connected' => $isConnected,
            'masked_token' => $isConnected ? substr($token, 0, 4).'••••••••'.substr($token, -4) : null,
            'device' => AppSetting::get(self::SETTING_DEVICE, '—'),
            'name' => AppSetting::get(self::SETTING_NAME, '—'),
            'device_status' => AppSetting::get(self::SETTING_STATUS, 'disconnect'),
        ];
    }

    /**
     * Kirim pesan WhatsApp via Fonnte.
     */
    public function sendMessage(string $target, string $message): array
    {
        $token = $this->getToken();
        if (! $token) {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum dikonfigurasi.',
            ];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $token,
                ])
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                ]);

            $data = $response->json();

            return [
                'success' => $response->successful() && ($data['status'] ?? false) === true,
                'data' => $data,
                'message' => $data['reason'] ?? ($data['status'] ? 'Pesan terkirim' : 'Gagal mengirim pesan'),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Koneksi Fonnte error: '.$e->getMessage(),
            ];
        }
    }
}

