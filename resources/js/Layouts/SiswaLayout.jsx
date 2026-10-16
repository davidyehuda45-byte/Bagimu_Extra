import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { 
    LayoutDashboard, 
    ClipboardList, 
    IdCard, 
    CalendarCheck, 
    FileText, 
    User, 
    LogOut,
    CheckCircle,
    AlertCircle
} from 'lucide-react';

export default function SiswaLayout({ children, title, subtitle }) {
    const { auth, flash } = usePage().props;
    const currentUrl = typeof window !== 'undefined' ? window.location.pathname : '';

    const navItems = [
        { label: 'Beranda', href: '/siswa', icon: LayoutDashboard, exact: true },
        { label: 'Pendaftaran', href: '/siswa/daftar', icon: ClipboardList },
        { label: 'Bukti Daftar', href: '/siswa/status', icon: ClipboardList },
        { label: 'Presensi', href: '/siswa/kehadiran', icon: CalendarCheck },
        { label: 'Profil', href: '/siswa/profil', icon: User },
    ];

    const isActive = (item) => {
        if (item.exact) return currentUrl === item.href;
        return currentUrl.startsWith(item.href);
    };

    return (
        <div className="min-h-screen bg-slate-50 flex flex-col justify-between text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white pb-20 md:pb-6">
            {/* Topbar Nav Siswa */}
            <header className="border-b border-slate-200/80 bg-white/80 backdrop-blur-md sticky top-0 z-30">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                    <Link href="/siswa" className="flex items-center gap-3 group">
                        <div className="h-10 w-10 rounded-2xl bg-gradient-to-br from-blue-700 to-indigo-800 flex items-center justify-center text-white font-black text-lg shadow-md shadow-blue-600/20 group-hover:scale-105 transition-transform">
                            B
                        </div>
                        <div>
                            <span className="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight block">
                                Bagimu Extra
                            </span>
                            <span className="text-[10px] text-slate-500 font-medium block">
                                Portal Siswa
                            </span>
                        </div>
                    </Link>

                    {/* Navigasi Desktop */}
                    <nav className="hidden md:flex items-center gap-1">
                        {navItems.map((item) => {
                            const Icon = item.icon;
                            const active = isActive(item);
                            return (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    className={`flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold transition-all ${
                                        active
                                            ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20'
                                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                    }`}
                                >
                                    <Icon className="h-4 w-4" />
                                    <span>{item.label}</span>
                                </Link>
                            );
                        })}
                    </nav>

                    {/* Identitas User & Logout */}
                    <div className="flex items-center gap-3">
                        <div className="hidden sm:block text-right">
                            <span className="block text-xs font-bold text-slate-900 truncate max-w-[140px]">
                                {auth?.user?.name || 'Siswa'}
                            </span>
                            <span className="block text-[10px] text-slate-400 font-mono">
                                Role: Siswa
                            </span>
                        </div>

                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            className="inline-flex items-center justify-center h-9 w-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 transition-colors cursor-pointer"
                            title="Keluar Akun"
                        >
                            <LogOut className="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </header>

            {/* Flash Banners */}
            <div className="max-w-5xl mx-auto w-full px-4 sm:px-6 pt-4 space-y-2">
                {flash?.success && (
                    <div className="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-xs text-emerald-800 flex items-center gap-3 shadow-xs animate-fade-in">
                        <CheckCircle className="h-5 w-5 text-emerald-600 shrink-0" />
                        <span className="font-semibold">{flash.success}</span>
                    </div>
                )}
                {flash?.error && (
                    <div className="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-xs text-rose-800 flex items-center gap-3 shadow-xs animate-fade-in">
                        <AlertCircle className="h-5 w-5 text-rose-600 shrink-0" />
                        <span className="font-semibold">{flash.error}</span>
                    </div>
                )}
            </div>

            {/* Header Judul Halaman (Jika Diisi) */}
            {(title || subtitle) && (
                <div className="max-w-5xl mx-auto w-full px-4 sm:px-6 pt-4 pb-2">
                    {title && <h1 className="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{title}</h1>}
                    {subtitle && <p className="text-xs sm:text-sm text-slate-500 mt-0.5">{subtitle}</p>}
                </div>
            )}

            {/* Konten Halaman */}
            <main className="max-w-5xl mx-auto w-full px-4 sm:px-6 flex-1 py-4">
                {children}
            </main>

            {/* Mobile Bottom Navigation Bar (App-like experience) */}
            <div className="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 z-40 px-2 py-1 shadow-lg">
                <div className="flex items-center justify-around">
                    {navItems.map((item) => {
                        const Icon = item.icon;
                        const active = isActive(item);
                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={`flex flex-col items-center justify-center py-1 px-2 rounded-xl text-[10px] font-bold transition-all ${
                                    active ? 'text-blue-600 scale-105' : 'text-slate-400 hover:text-slate-600'
                                }`}
                            >
                                <Icon className="h-5 w-5" />
                                <span className="mt-0.5">{item.label}</span>
                            </Link>
                        );
                    })}
                </div>
            </div>
        </div>
    );
}

