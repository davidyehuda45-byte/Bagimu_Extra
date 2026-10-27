import React from 'react';
import { Head, Link } from '@inertiajs/react';
import SiswaLayout from '../../Layouts/SiswaLayout';
import Card, { CardHeader, CardBody } from '../../Components/Card';
import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import { 
    Trophy, 
    CalendarCheck, 
    IdCard, 
    ClipboardList, 
    ArrowRight, 
    Clock, 
    MapPin, 
    UserCheck,
    CheckCircle2,
    AlertTriangle
} from 'lucide-react';

export default function Dashboard({ 
    user, 
    periode, 
    registration, 
    eskul, 
    kehadiranSummary = { total: 0, hadir: 0, persentase: 100 } 
}) {
    const isRegistered = Boolean(registration);

    return (
        <SiswaLayout>
            <Head title="Dashboard Siswa - Bagimu Extra" />

            <div className="space-y-6">
                {/* Hero Card Siswa */}
                <div className="rounded-3xl bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="space-y-2">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/15 backdrop-blur-md text-blue-100">
                                <span>Tahun Ajaran {periode?.periode || '2026/2027'}</span>
                            </span>
                            <h1 className="text-2xl sm:text-3xl font-black tracking-tight">
                                Selamat Datang, {user?.name || 'Siswa'}!
                            </h1>
                            <p className="text-xs sm:text-sm text-blue-100 max-w-xl">
                                Kelola pendaftaran kegiatan ekstrakurikuler, pantau kartu anggota digital, dan periksa rekap presensi latihan mingguanmu.
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center gap-3">
                            {isRegistered ? (
                                <Link href="/siswa/status">
                                    <Button variant="secondary" size="md" icon={IdCard}>
                                        <span>Buka E-Card Digital</span>
                                    </Button>
                                </Link>
                            ) : (
                                <Link href="/siswa/daftar">
                                    <Button variant="success" size="md" icon={ClipboardList}>
                                        <span>Daftar Eskul Sekarang</span>
                                    </Button>
                                </Link>
                            )}
                        </div>
                    </div>

                    {/* Background decorative blob */}
                    <div className="absolute -bottom-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none" />
                </div>

                {/* Grid Status Eskul & Presensi */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {/* Status Ekstrakurikuler Aktif */}
                    <Card className="md:col-span-2">
                        <CardHeader 
                            title="Ekstrakurikuler Pilihan Anda" 
                            subtitle="Status keikutsertaan resmi di semester ini"
                            action={
                                isRegistered ? (
                                    <Badge variant="success" dot>Terdaftar Aktif</Badge>
                                ) : (
                                    <Badge variant="warning" dot>Belum Mendaftar</Badge>
                                )
                            }
                        />

                        {isRegistered ? (
                            <div className="space-y-4">
                                <div className="flex items-start gap-4 p-4 rounded-2xl bg-blue-50/60 border border-blue-100">
                                    <div className="h-12 w-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20">
                                        <Trophy className="h-6 w-6" />
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <h4 className="text-lg font-black text-slate-900 truncate">
                                            {registration.eskul_utama}
                                        </h4>
                                        <p className="text-xs text-slate-500 line-clamp-2 mt-0.5">
                                            {eskul?.description || 'Kegiatan ekstrakurikuler resmi SMK Bagimu Negeriku.'}
                                        </p>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                                        <UserCheck className="h-4 w-4 text-blue-600 shrink-0" />
                                        <div>
                                            <span className="text-[10px] uppercase font-bold text-slate-400 block">Guru Pembina</span>
                                            <span className="font-bold text-slate-800">{eskul?.pembina?.name || 'Belum ditentukan'}</span>
                                        </div>
                                    </div>
                                    <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                                        <Clock className="h-4 w-4 text-blue-600 shrink-0" />
                                        <div>
                                            <span className="text-[10px] uppercase font-bold text-slate-400 block">Jadwal Rutin</span>
                                            <span className="font-bold text-slate-800">
                                                {eskul?.jadwals?.[0] ? `${eskul.jadwals[0].hari}, ${eskul.jadwals[0].jam_mulai} WIB` : 'Mengikuti arahan pembina'}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ) : (
                            <div className="text-center py-8 px-4 rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                                <AlertTriangle className="h-10 w-10 text-amber-500 mx-auto mb-2" />
                                <h4 className="font-extrabold text-sm text-slate-900">Belum Ada Eskul yang Dipilih</h4>
                                <p className="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                                    Pendaftaran periode ini telah dibuka. Tentukan pilihan eskulmu sebelum batas waktu berakhir.
                                </p>
                                <Link href="/siswa/daftar">
                                    <Button variant="primary" size="sm" icon={ArrowRight}>
                                        Pilih Ekstrakurikuler
                                    </Button>
                                </Link>
                            </div>
                        )}
                    </Card>

                    {/* Ringkasan Kehadiran */}
                    <Card>
                        <CardHeader 
                            title="Ringkasan Presensi" 
                            subtitle="Persentase keaktifan"
                        />
                        <div className="flex flex-col items-center justify-center py-4 text-center">
                            <div className="relative inline-flex items-center justify-center">
                                <div className="h-24 w-24 rounded-full border-4 border-blue-100 flex items-center justify-center bg-blue-50/50">
                                    <span className="text-2xl font-black text-blue-700">
                                        {kehadiranSummary.persentase}%
                                    </span>
                                </div>
                            </div>
                            <span className="text-xs font-bold text-slate-700 mt-3">Tingkat Kehadiran</span>
                            <p className="text-[11px] text-slate-400 mt-0.5">
                                {kehadiranSummary.hadir} dari {kehadiranSummary.total} pertemuan diikuti
                            </p>
                        </div>
                        <div className="pt-3 border-t border-slate-100">
                            <Link href="/siswa/kehadiran" className="w-full">
                                <Button variant="outline" size="sm" className="w-full">
                                    Lihat Riwayat Presensi
                                </Button>
                            </Link>
                        </div>
                    </Card>
                </div>
            </div>
        </SiswaLayout>
    );
}

