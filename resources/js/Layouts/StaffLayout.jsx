import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { 
    LayoutDashboard, 
    Users, 
    GraduationCap, 
    ArrowUpCircle, 
    Trophy, 
    CalendarCheck, 
    Award, 
    Settings, 
    QrCode, 
    FileText, 
    LogOut,
    Menu,
    X,
    CheckCircle,
    AlertCircle
} from 'lucide-react';

export default function StaffLayout({ children, title, subtitle, actions }) {
    const { auth, flash } = usePage().props;
    const [mobileOpen, setMobileOpen] = useState(false);
    const currentUrl = typeof window !== 'undefined' ? window.location.pathname : '';

    const role = auth?.user?.role || 'admin';

    // Menu berdasarkan role
    const getNavItems = () => {
        if (role === 'pembina') {
            return [
                { label: 'Dashboard', href: '/pembina', icon: LayoutDashboard, exact: true },
                { label: 'Eskul Saya', href: '/pembina/eskul', icon: Trophy },
                { label: 'Scan QR Presensi', href: '/pembina/scan', icon: QrCode },
                { label: 'Rencana Kegiatan', href: '/pembina/kegiatan', icon: CalendarCheck },
            ];
        }

        if (role === 'wali_kelas') {
            return [
                { label: 'Dashboard Rombel', href: '/wali-kelas', icon: LayoutDashboard, exact: true },
                { label: 'Siswa Rombel', href: '/wali-kelas/siswa', icon: Users },
                { label: 'Nilai & Cetak Rapor', href: '/wali-kelas/nilai', icon: FileText },
            ];
        }

        // Default Admin / Operator
        return [
            { label: 'Dashboard', href: '/admin', icon: LayoutDashboard, exact: true },
            { label: 'Direktori Siswa', href: '/admin/students', icon: Users },
            { label: 'Rombel & Wali Kelas', href: '/admin/kelas', icon: GraduationCap },
            { label: 'Kenaikan Kelas', href: '/admin/kenaikan', icon: ArrowUpCircle },
            { label: 'Master Eskul', href: '/admin/eskuls', icon: Trophy },
            { label: 'Rekap Presensi', href: '/admin/kehadiran', icon: CalendarCheck },
            { label: 'Pengaturan Sekolah', href: '/admin/settings', icon: Settings },
        ];
    };

    const navItems = getNavItems();

    const isActive = (item) => {
        if (item.exact) return currentUrl === item.href;
        return currentUrl.startsWith(item.href);
    };

    return (
        <div className="min-h-screen bg-slate-50 flex text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white">
            {/* Sidebar Desktop */}
            <aside className="hidden lg:flex w-64 flex-col border-r border-slate-200/90 bg-white fixed inset-y-0 z-30">
                {/* Branding Sekolah */}
                <div className="h-16 flex items-center gap-3 px-5 border-b border-slate-100">
                    <div className="h-9 w-9 rounded-xl bg-blue-700 text-white font-black flex items-center justify-center text-base shadow-sm">
                        B
                    </div>
                    <div className="min-w-0">
                        <span className="font-extrabold text-sm text-slate-900 tracking-tight block truncate">
                            Bagimu Extra
                        </span>
                        <span className="text-[10px] text-slate-400 uppercase font-bold tracking-wider block truncate">
                            Panel {role.replace('_', ' ')}
                        </span>
                    </div>
                </div>

                {/* Nav Links */}
                <nav className="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    {navItems.map((item) => {
                        const Icon = item.icon;
                        const active = isActive(item);
                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={`flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all ${
                                    active
                                        ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/20'
                                        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                }`}
                            >
                                <Icon className="h-4 w-4 shrink-0" />
                                <span>{item.label}</span>
                            </Link>
                        );
                    })}
                </nav>

                {/* Footer User Info */}
                <div className="p-3 border-t border-slate-100">
                    <div className="flex items-center justify-between p-2 rounded-2xl bg-slate-50 border border-slate-100">
                        <div className="min-w-0 pr-2">
                            <span className="block text-xs font-bold text-slate-900 truncate">
                                {auth?.user?.name || 'Staf Sekolah'}
                            </span>
                            <span className="block text-[10px] text-slate-400 capitalize truncate">
                                {role.replace('_', ' ')}
                            </span>
                        </div>
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            className="h-8 w-8 rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 flex items-center justify-center transition-colors cursor-pointer"
                            title="Keluar"
                        >
                            <LogOut className="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </aside>

            {/* Mobile Header & Content Wrapper */}
            <div className="flex-1 lg:pl-64 flex flex-col min-h-screen">
                {/* Topbar Mobile */}
                <header className="lg:hidden h-16 bg-white border-b border-slate-200/80 px-4 flex items-center justify-between sticky top-0 z-30">
                    <div className="flex items-center gap-2.5">
                        <div className="h-8 w-8 rounded-xl bg-blue-700 text-white font-black flex items-center justify-center text-sm shadow-sm">
                            B
                        </div>
                        <span className="font-extrabold text-sm text-slate-900">Bagimu Extra</span>
                    </div>

                    <button
                        type="button"
                        onClick={() => setMobileOpen(!mobileOpen)}
                        className="h-9 w-9 rounded-xl border border-slate-200 bg-white text-slate-600 flex items-center justify-center hover:bg-slate-50"
                    >
                        {mobileOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                    </button>
                </header>

                {/* Mobile Drawer */}
                {mobileOpen && (
                    <div className="lg:hidden fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs flex" onClick={() => setMobileOpen(false)}>
                        <div className="w-64 bg-white h-full p-4 flex flex-col" onClick={(e) => e.stopPropagation()}>
                            <div className="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                                <span className="font-bold text-sm text-slate-900">Menu Navigasi</span>
                                <button type="button" onClick={() => setMobileOpen(false)} className="text-slate-400 p-1">
                                    <X className="h-5 w-5" />
                                </button>
                            </div>
                            <nav className="flex-1 space-y-1">
                                {navItems.map((item) => {
                                    const Icon = item.icon;
                                    const active = isActive(item);
                                    return (
                                        <Link
                                            key={item.href}
                                            href={item.href}
                                            onClick={() => setMobileOpen(false)}
                                            className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold ${
                                                active ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'
                                            }`}
                                        >
                                            <Icon className="h-4 w-4" />
                                            <span>{item.label}</span>
                                        </Link>
                                    );
                                })}
                            </nav>
                        </div>
                    </div>
                )}

                {/* Main Content Area */}
                <main className="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
                    {/* Flash Messages */}
                    {flash?.success && (
                        <div className="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-800 flex items-center gap-3 shadow-xs">
                            <CheckCircle className="h-5 w-5 text-emerald-600 shrink-0" />
                            <span className="font-semibold">{flash.success}</span>
                        </div>
                    )}
                    {flash?.error && (
                        <div className="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 flex items-center gap-3 shadow-xs">
                            <AlertCircle className="h-5 w-5 text-rose-600 shrink-0" />
                            <span className="font-semibold">{flash.error}</span>
                        </div>
                    )}

                    {/* Page Title & Actions */}
                    {(title || subtitle || actions) && (
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
                            <div>
                                {title && <h1 className="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{title}</h1>}
                                {subtitle && <p className="text-xs text-slate-500 mt-0.5">{subtitle}</p>}
                            </div>
                            {actions && <div className="flex items-center gap-2">{actions}</div>}
                        </div>
                    )}

                    {children}
                </main>
            </div>
        </div>
    );
}

