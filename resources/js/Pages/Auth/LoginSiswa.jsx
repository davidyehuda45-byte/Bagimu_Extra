import React, { useState } from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import GuestLayout from '../../Layouts/GuestLayout';
import Button from '../../Components/Button';
import Input from '../../Components/Input';
import Card from '../../Components/Card';
import { KeyRound, UserCheck, HelpCircle, ArrowRight, ShieldCheck } from 'lucide-react';

export default function LoginSiswa() {
    const [showHelp, setShowHelp] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        nis: '',
        token: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/siswa/login');
    };

    return (
        <GuestLayout>
            <Head title="Masuk Siswa - Bagimu Extra" />

            <div className="w-full max-w-md">
                <Card className="p-7 sm:p-9 shadow-lg border-slate-200">
                    {/* Header Card */}
                    <div className="text-center mb-6">
                        <div className="h-14 w-14 rounded-3xl bg-blue-50 border border-blue-200/80 text-blue-700 flex items-center justify-center mx-auto mb-3 shadow-xs">
                            <UserCheck className="h-7 w-7" />
                        </div>
                        <h2 className="text-xl font-extrabold text-slate-900 tracking-tight">
                            Portal Masuk Siswa
                        </h2>
                        <p className="text-xs text-slate-500 mt-1">
                            Gunakan NIS dan 8-digit Token Login Anda untuk melanjutkan.
                        </p>
                    </div>

                    {/* Form Login */}
                    <form onSubmit={submit} className="space-y-4">
                        <Input
                            label="Nomor Induk Siswa (NIS)"
                            name="nis"
                            type="text"
                            placeholder="Contoh: 10001"
                            value={data.nis}
                            onChange={(e) => setData('nis', e.target.value)}
                            error={errors.nis}
                            required
                            autoFocus
                        />

                        <div>
                            <Input
                                label="Token Login (8 Digit)"
                                name="token"
                                type="text"
                                placeholder="Contoh: 14112631"
                                value={data.token}
                                onChange={(e) => setData('token', e.target.value.trim().toUpperCase())}
                                error={errors.token}
                                icon={KeyRound}
                                required
                                maxLength={8}
                            />
                            <div className="flex justify-between items-center mt-1.5">
                                <span className="text-[11px] text-slate-400">
                                    Token berupa 8 kombinasi angka/huruf
                                </span>
                                <button
                                    type="button"
                                    onClick={() => setShowHelp(!showHelp)}
                                    className="text-[11px] font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 cursor-pointer"
                                >
                                    <HelpCircle className="h-3 w-3" />
                                    <span>Lupa token?</span>
                                </button>
                            </div>
                        </div>

                        {/* Petunjuk Token */}
                        {showHelp && (
                            <div className="rounded-2xl bg-amber-50 border border-amber-200/80 p-3.5 text-xs text-amber-900 animate-fade-in leading-relaxed">
                                <strong className="font-bold block mb-0.5">Informasi Token Siswa:</strong>
                                Token login dibagikan oleh Bagian Kesiswaan atau Wali Kelas Anda saat pembagian rombel. Jika hilang, silakan hubungi Tata Usaha (TU) untuk penerbitan token baru.
                            </div>
                        )}

                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            isLoading={processing}
                            className="w-full mt-2"
                        >
                            <span>Masuk ke Akun Saya</span>
                            <ArrowRight className="h-4 w-4 ml-1" />
                        </Button>
                    </form>

                    {/* Footer Card */}
                    <div className="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <div className="flex items-center gap-1.5 text-slate-500">
                            <ShieldCheck className="h-4 w-4 text-emerald-600" />
                            <span>Aman tanpa kata sandi</span>
                        </div>
                        <Link href="/admin/login" className="font-bold text-blue-600 hover:text-blue-800">
                            Masuk Staf &rarr;
                        </Link>
                    </div>
                </Card>
            </div>
        </GuestLayout>
    );
}

