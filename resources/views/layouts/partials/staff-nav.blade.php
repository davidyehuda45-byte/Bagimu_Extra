{{-- Navigasi staf: dikelompokkan per kategori untuk kejelasan navigasi. --}}
<div class="space-y-4">
    {{-- 1. Bagian Utama --}}
    <div>
        <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 select-none">
            Bagian Utama
        </div>
        <div class="space-y-0.5">
            @if ($isAdmin || $isKepsek)
                <a href="{{ route('admin.dashboard') }}" class="{{ $link(Route::is('admin.dashboard')) }}">
                    <x-icon name="home" class="h-4 w-4 shrink-0" />
                    <span>Dashboard</span>
                </a>
            @endif
            @if ($isPembina)
                <a href="{{ route('pembina.dashboard') }}" class="{{ $link(Route::is('pembina.dashboard')) }}">
                    <x-icon name="home" class="h-4 w-4 shrink-0" />
                    <span>Dashboard Saya</span>
                </a>
            @endif
        </div>
    </div>

    {{-- 2. Eskul & Kegiatan --}}
    @if ($isAdmin || $isKepsek || $isPembina)
        <div class="pt-3 border-t border-slate-100">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 select-none">
                Eskul & Kegiatan
            </div>
            <div class="space-y-0.5">
                @if ($isAdmin || $isKepsek)
                    <a href="{{ route('admin.registrations.index') }}" class="{{ $link(Route::is('admin.registrations.*')) }}">
                        <x-icon name="clipboard" class="h-4 w-4 shrink-0" />
                        <span>Data Pendaftar</span>
                    </a>
                @endif
                @if ($isAdmin)
                    <a href="{{ route('admin.eskuls.index') }}" class="{{ $link(Route::is('admin.eskuls.*') || Route::is('admin.jadwals.*')) }}">
                        <x-icon name="sliders" class="h-4 w-4 shrink-0" />
                        <span>Master Eskul</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="{{ $link(Route::is('admin.users.*')) }}">
                        <x-icon name="users" class="h-4 w-4 shrink-0" />
                        <span>Kelola Pembina</span>
                    </a>
                    <a href="{{ route('admin.invitations.index') }}" class="{{ $link(Route::is('admin.invitations.*')) }}">
                        <x-icon name="user-plus" class="h-4 w-4 shrink-0" />
                        <span>Undang Pembina</span>
                    </a>
                @endif
                @if ($isAdmin || $isKepsek)
                    <a href="{{ route('admin.rekap.index') }}" class="{{ $link(Route::is('admin.rekap.*')) }}">
                        <x-icon name="trophy" class="h-4 w-4 shrink-0" />
                        <span>Rekap Eskul</span>
                    </a>
                    <a href="{{ route('admin.kehadiran.index') }}" class="{{ $link(Route::is('admin.kehadiran.*')) }}">
                        <x-icon name="calendar-check" class="h-4 w-4 shrink-0" />
                        <span>Absensi</span>
                    </a>
                @endif
                @if ($isAdmin)
                    <a href="{{ route('admin.nilai.index') }}" class="{{ $link(Route::is('admin.nilai.*')) }}">
                        <x-icon name="file" class="h-4 w-4 shrink-0" />
                        <span>Nilai</span>
                    </a>
                    <a href="{{ route('admin.izin.index') }}" class="{{ $link(Route::is('admin.izin.*')) }}">
                        <x-icon name="inbox" class="h-4 w-4 shrink-0" />
                        <span>Perizinan</span>
                    </a>
                    <a href="{{ route('admin.certificates.index') }}" class="{{ $link(Route::is('admin.certificates.*')) }}">
                        <x-icon name="award" class="h-4 w-4 shrink-0" />
                        <span>Sertifikat</span>
                    </a>
                    <a href="{{ route('admin.inventory.index') }}" class="{{ $link(Route::is('admin.inventory.*') || Route::is('admin.loans.*')) }}">
                        <x-icon name="package" class="h-4 w-4 shrink-0" />
                        <span>Inventaris</span>
                    </a>
                @endif
                @if ($isPembina)
                    <a href="{{ route('pembina.eskul.index') }}" class="{{ $link(Route::is('pembina.eskul.*')) }}">
                        <x-icon name="trophy" class="h-4 w-4 shrink-0" />
                        <span>Eskul Saya</span>
                    </a>
                    <a href="{{ route('pembina.kegiatan.index') }}" class="{{ $link(Route::is('pembina.kegiatan.*')) }}">
                        <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                        <span>Kegiatan</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- 3. Data Kesiswaan --}}
    @if ($isAdmin || $isKepsek)
        <div class="pt-3 border-t border-slate-100">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 select-none">
                Data Kesiswaan
            </div>
            <div class="space-y-0.5">
                <a href="{{ route('admin.students.index') }}" class="{{ $link(Route::is('admin.students.index') || Route::is('admin.students.create') || Route::is('admin.students.edit') || Route::is('admin.students.show')) }}">
                    <x-icon name="users" class="h-4 w-4 shrink-0" />
                    <span>Direktori Siswa</span>
                </a>
                <a href="{{ route('admin.students.status-rombel') }}" class="{{ $link(Route::is('admin.students.status-rombel') || Route::is('admin.students.cetak-kartu-rombel')) }}">
                    <x-icon name="chart" class="h-4 w-4 shrink-0" />
                    <span>Status Rombel</span>
                </a>
                <a href="{{ route('admin.kelas.index') }}" class="{{ $link(Route::is('admin.kelas.*')) }}">
                    <x-icon name="grad" class="h-4 w-4 shrink-0" />
                    <span>Kelas</span>
                </a>
                @if ($isAdmin)
                    <a href="{{ route('admin.kenaikan.index') }}" class="{{ $link(Route::is('admin.kenaikan.*')) }}">
                        <x-icon name="trending-up" class="h-4 w-4 shrink-0" />
                        <span>Kenaikan Kelas</span>
                    </a>
                    <a href="{{ route('admin.kartu.index') }}" class="{{ $link(Route::is('admin.kartu.*')) }}">
                        <x-icon name="key" class="h-4 w-4 shrink-0" />
                        <span>Kartu Login</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- 4. Informasi & Laporan --}}
    @if ($isAdmin || $isKepsek)
        <div class="pt-3 border-t border-slate-100">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 select-none">
                Informasi & Laporan
            </div>
            <div class="space-y-0.5">
                @if ($isAdmin)
                    <a href="{{ route('admin.notifikasi.index') }}" class="{{ $link(Route::is('admin.notifikasi.*')) }}">
                        <x-icon name="bell" class="h-4 w-4 shrink-0" />
                        <span class="flex-1">Notifikasi</span>
                        @php($notifBelum = \App\Support\Notifier::belumDibaca(auth()->id()))
                        @if ($notifBelum > 0)
                            <span class="rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-bold text-white leading-none">
                                {{ $notifBelum > 99 ? '99+' : $notifBelum }}
                            </span>
                        @endif
                    </a>
                @endif
                <a href="{{ route('admin.laporan.index') }}" class="{{ $link(Route::is('admin.laporan.*')) }}">
                    <x-icon name="chart" class="h-4 w-4 shrink-0" />
                    <span>Laporan</span>
                </a>
                @if ($isAdmin)
                    <a href="{{ route('admin.cetak.index') }}" class="{{ $link(Route::is('admin.cetak.*')) }}">
                        <x-icon name="printer" class="h-4 w-4 shrink-0" />
                        <span>Cetak</span>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- 5. Sistem & Admin --}}
    @if ($isAdmin || $isKepsek)
        <div class="pt-3 border-t border-slate-100">
            <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 select-none">
                Sistem & Admin
            </div>
            <div class="space-y-0.5">
                @if ($isAdmin)
                    <a href="{{ route('admin.periodes.index') }}" class="{{ $link(Route::is('admin.periodes.*')) }}">
                        <x-icon name="calendar" class="h-4 w-4 shrink-0" />
                        <span>Periode & Arsip</span>
                    </a>
                    <a href="{{ route('admin.windows.index') }}" class="{{ $link(Route::is('admin.windows.*')) }}">
                        <x-icon name="clock" class="h-4 w-4 shrink-0" />
                        <span>Jendela</span>
                    </a>
                @endif
                <a href="{{ route('admin.logs.index') }}" class="{{ $link(Route::is('admin.logs.*')) }}">
                    <x-icon name="history" class="h-4 w-4 shrink-0" />
                    <span>Riwayat</span>
                </a>
                @if ($isAdminOnly)
                    <a href="{{ route('admin.settings.index') }}" class="{{ $link(Route::is('admin.settings.*')) }}">
                        <x-icon name="shield" class="h-4 w-4 shrink-0" />
                        <span>Pengaturan</span>
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
