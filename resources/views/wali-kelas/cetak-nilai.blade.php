<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Nilai Ekstrakurikuler Rombel {{ $kelas?->nama }} - T.A. {{ $periode?->periode }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }
        body {
            background-color: #f8fafc;
            color: #000;
            padding: 20px;
        }
        .page {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 20mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .kop h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 2px;
        }
        .kop p {
            font-size: 10pt;
            margin-top: 4px;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        .title h3 {
            font-size: 12pt;
            text-transform: uppercase;
            text-decoration: underline;
            font-weight: bold;
        }
        .info-meta {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10.5pt;
        }
        .info-meta td {
            padding: 3px 0;
            vertical-align: top;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 30px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .ttd-box {
            width: 100%;
            margin-top: 30px;
            font-size: 10.5pt;
        }
        .ttd-box td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .print-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 999;
        }
        .btn-print {
            background-color: #1e3a8a;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 12px;
            font-family: sans-serif;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }
        .btn-close {
            background-color: #64748b;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-family: sans-serif;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 15mm;
                max-width: 100%;
                min-height: auto;
            }
            .print-bar {
                display: none !important;
            }
            table.data-table th {
                background-color: #eee !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="print-bar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <button class="btn-close" onclick="window.close()">Tutup</button>
    </div>

    <div class="page">
        {{-- Kop Dokumen Resmi --}}
        <div class="kop">
            <h1>{{ $sekolahNama }}</h1>
            <h2>DAFTAR NILAI KEGIATAN EKSTRAKURIKULER</h2>
            <p>{{ $sekolahAlamat }}</p>
        </div>

        <div class="title">
            <h3>LAMPIRAN NILAI EKSTRAKURIKULER UNTUK BUKU RAPOR</h3>
        </div>

        <table class="info-meta">
            <tr>
                <td style="width: 18%;"><strong>Rombongan Belajar</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 45%;">{{ $kelas?->nama ?? '-' }}</td>
                <td style="width: 15%;"><strong>Tahun Ajaran</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 18%;">{{ $periode?->periode ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Wali Kelas</strong></td>
                <td>:</td>
                <td>{{ $kelas?->wali_kelas_nama ?? auth()->user()->name }}</td>
                <td><strong>Semester</strong></td>
                <td>:</td>
                <td>{{ \App\Models\AppSetting::get('eskul.semester_aktif', '1') == '1' ? '1 (Ganjil)' : '2 (Genap)' }}</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 14%;">NIS</th>
                    <th style="width: 28%;">Nama Lengkap Siswa</th>
                    <th style="width: 22%;">Kegiatan Ekstrakurikuler</th>
                    <th style="width: 8%;">Nilai</th>
                    <th style="width: 8%;">Predikat</th>
                    <th style="width: 15%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataNilai as $index => $row)
                    @php
                        $pred = $row->predikat ?: ($row->nilai !== null ? ($row->nilai >= 85 ? 'A' : ($row->nilai >= 70 ? 'B' : ($row->nilai >= 55 ? 'C' : 'D'))) : '-');
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $row->no_absen ?? ($index + 1) }}</td>
                        <td style="text-align: center; font-family: monospace;">{{ $row->nis }}</td>
                        <td>{{ $row->student?->nama ?? $row->user?->name ?? '-' }}</td>
                        <td>{{ $row->eskul_utama }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $row->nilai !== null ? $row->nilai : '-' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $pred }}</td>
                        <td style="font-size: 8.5pt;">{{ $row->catatan_nilai ?: ($row->nilai ? 'Sangat Baik' : 'Belum Terisi') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">Belum ada data nilai ekstrakurikuler siswa di rombel ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tanda Tangan --}}
        <table class="ttd-box">
            <tr>
                <td>
                    Mengetahui,<br>
                    Kepala Sekolah<br><br><br><br><br>
                    <strong><u>{{ $kepsekNama }}</u></strong>
                </td>
                <td>
                    Semarang, {{ now()->translatedFormat('d F Y') }}<br>
                    Wali Kelas {{ $kelas?->nama }}<br><br><br><br><br>
                    <strong><u>{{ $kelas?->wali_kelas_nama ?? auth()->user()->name }}</u></strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

