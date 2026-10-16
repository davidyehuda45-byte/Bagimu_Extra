import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { AlertCircle, CheckCircle } from 'lucide-react';

export default function GuestLayout({ children, title }) {
    const { flash } = usePage().props;

    return (
        <div className="min-h-screen bg-slate-50 flex flex-col justify-between text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white">
            {/* Header Sekolah */}
            <header className="border-b border-slate-200/80 bg-white/80 backdrop-blur-md sticky top-0 z-30">
                <div className="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                    <Link href="/" className="flex items-center gap-3 group">
                        <div className="h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-700 to-indigo-800 flex items-center justify-center text-white font-black text-lg shadow-md shadow-blue-600/20 group-hover:scale-105 transition-transform">
                            B
                        </div>
                        <div>
                            <span className="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight block">
                                Bagimu Extra
                            </span>
                            <span className="text-[10px] text-slate-500 font-medium block">
                                SMK Bagimu Negeriku
                            </span>
                        </div>
                    </Link>

                    <div className="flex items-center gap-2">
                        <Link
                            href="/jadwal"
                            className="text-xs font-semibold text-slate-600 hover:text-blue-700 px-3 py-1.5 rounded-lg hover:bg-slate-100 transition-colors hidden sm:inline-block"
                        >
                            Jadwal Eskul
                        </Link>
                        <Link
                            href="/admin/login"
                            className="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200/80 hover:bg-blue-100 px-3.5 py-1.5 rounded-xl transition-all"
                        >
                            Portal Staf
                        </Link>
                    </div>
                </div>
            </header>

            {/* Flash Alerts */}
            {flash?.success && (
                <div className="max-w-md mx-auto w-full px-4 pt-4">
                    <div className="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800 flex items-center gap-3 shadow-xs">
                        <CheckCircle className="h-5 w-5 text-emerald-600 shrink-0" />
                        <span>{flash.success}</span>
                    </div>
                </div>
            )}
            {flash?.error && (
                <div className="max-w-md mx-auto w-full px-4 pt-4">
                    <div className="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 flex items-center gap-3 shadow-xs">
                        <AlertCircle className="h-5 w-5 text-rose-600 shrink-0" />
                        <span>{flash.error}</span>
                    </div>
                </div>
            )}

            {/* Konten Utama */}
            <main className="flex-1 flex items-center justify-center p-4 sm:p-6 my-6">
                {children}
            </main>

            {/* Footer */}
            <footer className="border-t border-slate-200/80 bg-white py-6 text-center text-xs text-slate-500">
                <p>&copy; {new Date().getFullYear()} SMK Bagimu Negeriku Semarang. Sistem Pengelolaan Ekstrakurikuler.</p>
            </footer>
        </div>
    );
}

