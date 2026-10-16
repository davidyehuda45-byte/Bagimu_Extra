import React from 'react';
import { Head, Link } from '@inertiajs/react';
import SiswaLayout from '../../Layouts/SiswaLayout';
import Card from '../../Components/Card';
import Button from '../../Components/Button';
import Badge from '../../Components/Badge';
import { 
    Printer, 
    ArrowLeft, 
    ShieldCheck, 
    Calendar, 
    User, 
    CheckCircle2, 
    Sparkles 
} from 'lucide-react';

export default function StatusECard({ 
    user, 
    registration, 
    periode, 
    schoolName = 'SMK Bagimu Negeriku',
    qrCodeSvg = null,
    verifyUrl = ''
}) {
    const handlePrint = () => {
        window.print();
    };

    if (!registration) {
        return (
            <SiswaLayout title="Kartu Anggota Digital">
                <div className="text-center py-12">
                    <p className="text-sm text-slate-500 mb-4">Anda belum terdaftar pada ekstrakurikuler mana pun.</p>
                    <Link href="/siswa/daftar">
                        <Button variant="primary">Daftar Sekarang</Button>
                    </Link>
                </div>
            </SiswaLayout>
        );
    }

    return (
        <SiswaLayout title="E-Card Ekstrakurikuler Digital">
            <Head title="Kartu Anggota Digital - Bagimu Extra" />

            <div className="max-w-xl mx-auto space-y-6">
                {/* Petunjuk Cetak (Hilang saat cetak) */}
                <div className="no-print rounded-2xl bg-blue-50/80 border border-blue-200/80 p-4 text-xs text-blue-900 flex items-center justify-between gap-4">
                    <div className="flex items-center gap-2.5">
                        <ShieldCheck className="h-5 w-5 text-blue-600 shrink-0" />
                        <span>Tunjukkan QR Code ini kepada Pembina saat latihan untuk presensi digital.</span>
                    </div>
                    <Button variant="outline" size="sm" onClick={handlePrint} icon={Printer}>
                        Cetak E-Card
                    </Button>
                </div>

                {/* Digital Card Preview (Print Area) */}
                <div className="print-area rounded-3xl overflow-hidden border border-slate-200 shadow-xl bg-white transition-all">
                    {/* Header Kartu */}
                    <div className="bg-gradient-to-r from-blue-800 via-blue-900 to-indigo-950 p-6 text-white text-center relative overflow-hidden">
                        <div className="relative z-10 flex flex-col items-center">
                            <span className="text-[10px] tracking-widest font-black uppercase text-blue-200">
                                {schoolName}
                            </span>
                            <h2 className="text-lg font-black tracking-tight mt-0.5">
                                KARTU ANGGOTA EKSTRAKURIKULER
                            </h2>
                            <span className="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/15 text-blue-100">
                                Tahun Ajaran {periode?.periode || registration.periode}
                            </span>
                        </div>
                        {/* Decorative Background */}
                        <div className="absolute -top-10 -left-10 w-32 h-32 bg-blue-500/20 rounded-full blur-xl" />
                        <div className="absolute -bottom-10 -right-10 w-32 h-32 bg-indigo-500/20 rounded-full blur-xl" />
                    </div>

                    {/* Badan Kartu */}
                    <div className="p-6 sm:p-8 space-y-6">
                        <div className="flex flex-col sm:flex-row items-center gap-6">
                            {/* Avatar Siswa */}
                            <div className="h-24 w-24 rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black text-3xl shadow-md shrink-0 border-4 border-white">
                                {user?.name ? user.name.charAt(0).toUpperCase() : 'S'}
                            </div>

                            {/* Biodata Singkat */}
                            <div className="text-center sm:text-left space-y-1 min-w-0 flex-1">
                                <Badge variant="success" dot size="sm">Anggota Resmi Terverifikasi</Badge>
                                <h3 className="text-xl font-black text-slate-900 truncate">
                                    {user?.name}
                                </h3>
                                <div className="text-xs text-slate-500 flex flex-wrap justify-center sm:justify-start gap-x-3 gap-y-1">
                                    <span>NIS: <strong className="text-slate-800 font-mono">{registration.nis || user?.username}</strong></span>
                                    <span>•</span>
                                    <span>Kelas: <strong className="text-slate-800">{registration.kelas || '-'}</strong></span>
                                    <span>•</span>
                                    <span>Jurusan: <strong className="text-slate-800">{registration.jurusan || '-'}</strong></span>
                                </div>
                            </div>
                        </div>

                        {/* Eskul Banner Box */}
                        <div className="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-center sm:text-left flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span className="text-[10px] uppercase font-bold text-slate-400 block">Ekstrakurikuler Resmi</span>
                                <h4 className="text-base font-extrabold text-blue-950">{registration.eskul_utama}</h4>
                            </div>
                            <span className="px-3 py-1 bg-white rounded-xl border border-blue-200 text-xs font-bold text-blue-800 shadow-2xs self-center sm:self-auto">
                                Status: AKTIF
                            </span>
                        </div>

                        {/* QR Code Presensi Area */}
                        <div className="pt-4 border-t border-slate-100 flex flex-col items-center justify-center text-center space-y-3">
                            <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                QR Code Presensi Pertemuan
                            </span>
                            
                            {/* QR Render Container */}
                            <div className="p-3 bg-white border-2 border-slate-200/80 rounded-2xl shadow-inner inline-block">
                                {qrCodeSvg ? (
                                    <div 
                                        className="h-36 w-36 flex items-center justify-center" 
                                        dangerouslySetInnerHTML={{ __html: qrCodeSvg }} 
                                    />
                                ) : (
                                    <div className="h-36 w-36 bg-slate-100 rounded-xl flex flex-col items-center justify-center p-2 text-slate-400">
                                        <ShieldCheck className="h-8 w-8 mb-1 text-slate-400" />
                                        <span className="text-[10px] font-mono text-center">QR Token: {registration.verify_token ? registration.verify_token.substring(0, 8) : 'ACTIVE'}</span>
                                    </div>
                                )}
                            </div>

                            <p className="text-[10px] text-slate-400 max-w-xs">
                                Pindai kartu ini melalui kamera perangkat guru pembina saat pertemuan ekstrakurikuler berlangsung.
                            </p>
                        </div>
                    </div>

                    {/* Footer Kartu */}
                    <div className="bg-slate-50 px-6 py-3 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                        <span>Sistem Ekstrakurikuler Bagimu Extra</span>
                        <span className="font-mono">{new Date().toLocaleDateString('id-ID')}</span>
                    </div>
                </div>

                {/* Tombol Navigasi Bawah */}
                <div className="no-print flex justify-between items-center pt-2">
                    <Link href="/siswa">
                        <Button variant="ghost" size="sm" icon={ArrowLeft}>
                            Kembali ke Beranda
                        </Button>
                    </Link>
                    <Button variant="primary" size="sm" onClick={handlePrint} icon={Printer}>
                        Cetak Kartu Siswa
                    </Button>
                </div>
            </div>
        </SiswaLayout>
    );
}

