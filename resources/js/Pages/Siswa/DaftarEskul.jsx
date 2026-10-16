import React, { useState } from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import SiswaLayout from '../../Layouts/SiswaLayout';
import Card, { CardHeader } from '../../Components/Card';
import Button from '../../Components/Button';
import Badge from '../../Components/Badge';
import Modal from '../../Components/Modal';
import { 
    Trophy, 
    Calendar, 
    Clock, 
    User, 
    Check, 
    AlertCircle, 
    IdCard, 
    ChevronRight,
    Users
} from 'lucide-react';

export default function DaftarEskul({ 
    user, 
    periode, 
    existing, 
    existingEskul, 
    eskuls = [], 
    windowOpen = true, 
    windowLabel = '' 
}) {
    const [selectedEskulName, setSelectedEskulName] = useState('');
    const [isConfirmModalOpen, setIsConfirmModalOpen] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        eskul_utama: '',
    });

    const isAlreadyRegistered = Boolean(existing);
    const selectedEskulObj = eskuls.find((e) => e.name === selectedEskulName);

    const handleSelectEskul = (name) => {
        setSelectedEskulName(name);
        setData('eskul_utama', name);
    };

    const handleOpenConfirm = () => {
        if (!data.eskul_utama) return;
        setIsConfirmModalOpen(true);
    };

    const handleSubmitRegistration = () => {
        post('/siswa/daftar', {
            onSuccess: () => {
                setIsConfirmModalOpen(false);
            },
        });
    };

    return (
        <SiswaLayout
            title="Pendaftaran Ekstrakurikuler"
            subtitle={`Tahun Ajaran ${periode?.periode || '2026/2027'} • SMK Bagimu Negeriku`}
        >
            <Head title="Pilih Ekstrakurikuler - Bagimu Extra" />

            <div className="space-y-6">
                {/* Jendela Tertutup / Periode Belum Dibuka */}
                {!windowOpen && !isAlreadyRegistered && (
                    <div className="rounded-3xl border border-amber-200 bg-amber-50/80 p-8 text-center shadow-xs">
                        <AlertCircle className="h-10 w-10 text-amber-600 mx-auto mb-2" />
                        <h3 className="text-base font-extrabold text-amber-950">Pendaftaran Sedang Ditutup</h3>
                        <p className="text-xs text-amber-800 max-w-md mx-auto mt-1">
                            {windowLabel || 'Jadwal pendaftaran ekstrakurikuler saat ini belum aktif. Hubungi kesiswaan untuk informasi lebih lanjut.'}
                        </p>
                    </div>
                )}

                {/* Siswa Sudah Terdaftar */}
                {isAlreadyRegistered && (
                    <Card className="border-emerald-200 bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/40 p-6 sm:p-8">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-emerald-100">
                            <div className="flex items-center gap-4">
                                <div className="h-14 w-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20 shrink-0">
                                    <Check className="h-7 w-7" />
                                </div>
                                <div>
                                    <Badge variant="success" dot>Terdaftar & Diterima Resmi</Badge>
                                    <h2 className="text-2xl font-black text-slate-900 mt-1">
                                        {existing.eskul_utama}
                                    </h2>
                                    <p className="text-xs text-slate-500">
                                        Pendaftaran Anda telah disimpan permanen.
                                    </p>
                                </div>
                            </div>

                            <Link href="/siswa/status">
                                <Button variant="primary" size="md" icon={IdCard}>
                                    Lihat E-Card Digital
                                </Button>
                            </Link>
                        </div>

                        {/* Rincian Eskul Terdaftar */}
                        <div className="pt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div className="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                                <span className="font-bold text-slate-400 uppercase text-[10px] block">Guru Pembina</span>
                                <span className="font-extrabold text-slate-900 text-sm mt-0.5 block">
                                    {existingEskul?.pembina?.name || 'Belum ditentukan'}
                                </span>
                            </div>
                            <div className="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs">
                                <span className="font-bold text-slate-400 uppercase text-[10px] block">Jadwal Pertemuan</span>
                                <span className="font-extrabold text-slate-900 text-sm mt-0.5 block">
                                    {existingEskul?.jadwals?.[0] ? `${existingEskul.jadwals[0].hari}, ${existingEskul.jadwals[0].jam_mulai} WIB` : 'Akan diumumkan pembina'}
                                </span>
                            </div>
                        </div>
                    </Card>
                )}

                {/* Form Pendaftaran Eskul (Jika Terbuka dan Belum Terdaftar) */}
                {!isAlreadyRegistered && windowOpen && (
                    <div className="space-y-6">
                        {/* Notice Alert */}
                        <div className="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 text-xs text-blue-900 flex items-start gap-3">
                            <AlertCircle className="h-5 w-5 text-blue-600 shrink-0 mt-0.5" />
                            <div>
                                <strong className="font-bold block">Pemberitahuan:</strong>
                                Pendaftaran eskul hanya dilakukan 1 (satu) kali dan langsung disetujui tanpa antrean. Pastikan pilihanmu sudah tepat sebelum konfirmasi.
                            </div>
                        </div>

                        {/* Grid Pilihan Eskul */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {eskuls.map((eskulItem) => {
                                const isSelected = selectedEskulName === eskulItem.name;
                                const isPenuh = eskulItem.penuh;
                                const kuotaPct = eskulItem.kuota > 0 ? Math.min(100, Math.round((eskulItem.terisi / eskulItem.kuota) * 100)) : 0;

                                return (
                                    <div
                                        key={eskulItem.name}
                                        onClick={() => !isPenuh && handleSelectEskul(eskulItem.name)}
                                        className={`rounded-3xl border p-5 transition-all duration-200 cursor-pointer ${
                                            isPenuh
                                                ? 'bg-slate-50 border-slate-200 opacity-60 cursor-not-allowed'
                                                : isSelected
                                                ? 'bg-white border-blue-600 shadow-md ring-2 ring-blue-600/20'
                                                : 'bg-white border-slate-200 hover:border-slate-300 shadow-xs'
                                        }`}
                                    >
                                        <div className="flex items-start justify-between gap-3 mb-2">
                                            <div className="flex items-center gap-2.5">
                                                <div className={`h-10 w-10 rounded-2xl flex items-center justify-center font-bold text-sm ${
                                                    isSelected ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700'
                                                }`}>
                                                    <Trophy className="h-5 w-5" />
                                                </div>
                                                <div>
                                                    <h4 className="font-extrabold text-base text-slate-900">
                                                        {eskulItem.name}
                                                    </h4>
                                                    <span className="text-[11px] text-slate-500">
                                                        Pembina: {eskulItem.pembina_nama || 'Belum diisi'}
                                                    </span>
                                                </div>
                                            </div>

                                            {isPenuh ? (
                                                <Badge variant="danger">Penuh</Badge>
                                            ) : isSelected ? (
                                                <Badge variant="info" dot>Dipilih</Badge>
                                            ) : null}
                                        </div>

                                        <p className="text-xs text-slate-600 line-clamp-2 my-3">
                                            {eskulItem.description || 'Kegiatan ekstrakurikuler resmi SMK Bagimu Negeriku.'}
                                        </p>

                                        {/* Kuota Bar */}
                                        {eskulItem.kuota_aktif && (
                                            <div className="mb-3 space-y-1">
                                                <div className="flex justify-between text-[11px] text-slate-500 font-semibold">
                                                    <span>Kapasitas</span>
                                                    <span>{eskulItem.terisi} / {eskulItem.kuota} Siswa</span>
                                                </div>
                                                <div className="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                                    <div 
                                                        className={`h-full rounded-full transition-all ${
                                                            kuotaPct >= 90 ? 'bg-rose-500' : kuotaPct >= 70 ? 'bg-amber-500' : 'bg-blue-600'
                                                        }`}
                                                        style={{ width: `${kuotaPct}%` }}
                                                    />
                                                </div>
                                            </div>
                                        )}

                                        {/* Jadwal Info */}
                                        <div className="pt-2 border-t border-slate-100 flex items-center gap-2 text-[11px] text-slate-500">
                                            <Calendar className="h-3.5 w-3.5 text-blue-600" />
                                            <span>{eskulItem.jadwal || 'Jadwal latihan menyusul'}</span>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        {errors.eskul_utama && (
                            <p className="text-xs font-bold text-rose-600 flex items-center gap-1">
                                <AlertCircle className="h-4 w-4" />
                                <span>{errors.eskul_utama}</span>
                            </p>
                        )}

                        {/* Submit Action Bar */}
                        <div className="pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <span className="text-xs text-slate-500">
                                Eskul terpilih: <strong className="text-slate-900">{selectedEskulName || 'Belum memilih'}</strong>
                            </span>
                            <Button
                                variant="primary"
                                size="lg"
                                disabled={!selectedEskulName}
                                onClick={handleOpenConfirm}
                                icon={ChevronRight}
                            >
                                Lanjutkan Konfirmasi Pendaftaran
                            </Button>
                        </div>
                    </div>
                )}
            </div>

            {/* Modal Konfirmasi Pendaftaran */}
            <Modal
                isOpen={isConfirmModalOpen}
                onClose={() => setIsConfirmModalOpen(false)}
                title="Konfirmasi Pilihan Ekstrakurikuler"
                description="Pastikan data pendaftaran Anda sudah tepat."
            >
                <div className="space-y-4">
                    <div className="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                        <div className="flex justify-between">
                            <span className="text-slate-500">Nama Siswa:</span>
                            <span className="font-bold text-slate-900">{user?.name}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-slate-500">NIS:</span>
                            <span className="font-bold text-slate-900">{user?.student?.nis || user?.username}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-slate-500">Pilihan Eskul:</span>
                            <span className="font-extrabold text-blue-700 text-sm">{selectedEskulName}</span>
                        </div>
                    </div>

                    <div className="rounded-xl bg-amber-50 p-3 text-xs text-amber-900 border border-amber-200/80 flex items-start gap-2">
                        <AlertCircle className="h-4 w-4 shrink-0 mt-0.5 text-amber-600" />
                        <span>Data yang dikirim akan disimpan secara permanen dan tidak dapat diubah kembali.</span>
                    </div>

                    <div className="flex gap-2 pt-2">
                        <Button
                            variant="secondary"
                            size="md"
                            className="flex-1"
                            onClick={() => setIsConfirmModalOpen(false)}
                        >
                            Periksa Kembali
                        </Button>
                        <Button
                            variant="primary"
                            size="md"
                            className="flex-1"
                            isLoading={processing}
                            onClick={handleSubmitRegistration}
                        >
                            Ya, Kirim Pendaftaran
                        </Button>
                    </div>
                </div>
            </Modal>
        </SiswaLayout>
    );
}

