<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran Ekstrakurikuler</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07); border: 1px solid #e2e8f0;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #1d4ed8; padding: 28px 24px; text-align: left; color: #ffffff;">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #bfdbfe; margin-bottom: 4px;">
                                {{ \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra')) }}
                            </div>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 800; color: #ffffff;">
                                Bukti Pendaftaran Ekstrakurikuler
                            </h1>
                            <p style="margin: 6px 0 0; font-size: 13px; color: #dbeafe;">
                                Data pendaftaran resmi tercatat pada sistem sekolah.
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 24px;">
                            <p style="margin: 0 0 16px; font-size: 15px; color: #334155;">
                                Halo <strong>{{ $registration->user?->name ?? 'Siswa' }}</strong>,
                            </p>
                            <p style="margin: 0 0 20px; font-size: 14px; color: #475569; line-height: 1.6;">
                                Berikut adalah bukti resmi pendaftaran kegiatan ekstrakurikuler Anda untuk periode <strong>{{ $registration->periode }}</strong>. Simpan email ini sebagai tanda bukti kepesertaan Anda.
                            </p>

                            <!-- Data Siswa Card -->
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="6" style="font-size: 13px;">
                                    <tr>
                                        <td width="35%" style="color: #64748b; font-weight: 500;">Kode Pendaftaran</td>
                                        <td style="color: #0f172a; font-weight: 700; font-family: monospace; font-size: 14px;">{{ $registration->kode() }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 500;">Nama Lengkap</td>
                                        <td style="color: #0f172a; font-weight: 600;">{{ $registration->user?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 500;">NIS</td>
                                        <td style="color: #0f172a;">{{ $registration->nis ?? $registration->user?->username ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 500;">Kelas / Jurusan</td>
                                        <td style="color: #0f172a;">{{ $registration->kelas }} • {{ $registration->jurusan?->label() ?? $registration->jurusan?->value ?? '—' }}</td>
                                    </tr>
                                    @if ($registration->no_absen)
                                    <tr>
                                        <td style="color: #64748b; font-weight: 500;">No. Absen</td>
                                        <td style="color: #0f172a;">{{ $registration->no_absen }}</td>
                                    </tr>
                                    @endif
                                    @if ($registration->nama_wali_kelas)
                                    <tr>
                                        <td style="color: #64748b; font-weight: 500;">Wali Kelas</td>
                                        <td style="color: #0f172a;">{{ $registration->nama_wali_kelas }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="color: #64748b; font-weight: 500;">Tanggal Daftar</td>
                                        <td style="color: #0f172a;">{{ $registration->created_at->format('d M Y') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Pilihan Eskul Card -->
                            <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #1d4ed8; margin-bottom: 6px;">Ekstrakurikuler Pilihan</div>
                                <div style="font-size: 16px; font-weight: 800; color: #1e3a8a; margin-bottom: 4px;">
                                    {{ $registration->eskul_utama }}
                                </div>
                                @if ($registration->eskul_cadangan)
                                    <div style="font-size: 12px; color: #3b82f6;">
                                        Pilihan Cadangan: {{ $registration->eskul_cadangan }}
                                    </div>
                                @endif

                                @if ($jadwals->isNotEmpty())
                                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #bfdbfe;">
                                        <div style="font-size: 12px; font-weight: 700; color: #1e40af; margin-bottom: 4px;">Jadwal Latihan:</div>
                                        <ul style="margin: 0; padding-left: 18px; font-size: 13px; color: #1e293b;">
                                            @foreach ($jadwals as $j)
                                                <li>{{ ucfirst(strtolower($j->hari)) }} • {{ $j->jam_mulai }}–{{ $j->jam_selesai }} ({{ $j->tempat }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <!-- Button Verification -->
                            @if (!empty($verifyUrl))
                                <div style="text-align: center; margin: 28px 0 20px;">
                                    <a href="{{ $verifyUrl }}" target="_blank" style="display: inline-block; background-color: #1d4ed8; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 700; font-size: 14px; box-shadow: 0 2px 4px rgba(29, 78, 216, 0.2);">
                                        Lihat Status & Verifikasi Online &rarr;
                                    </a>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 8px;">
                                        Atau buka langsung tautan: <br>
                                        <a href="{{ $verifyUrl }}" style="color: #2563eb; word-break: break-all;">{{ $verifyUrl }}</a>
                                    </div>
                                </div>
                            @endif

                            <p style="margin: 20px 0 0; font-size: 12px; color: #64748b; line-height: 1.5; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                                Jika terdapat kekeliruan data atau pertanyaan seputar jadwal kegiatan, silakan hubungi pengurus ekstrakurikuler atau staf Tata Usaha sekolah.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 16px 24px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 11px; color: #94a3b8;">
                            &copy; {{ date('Y') }} {{ \App\Models\AppSetting::get('school.name', config('app.name', 'Bagimu Extra')) }}. Email ini dikirim secara otomatis oleh sistem.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

