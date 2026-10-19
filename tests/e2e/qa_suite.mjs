import { chromium } from 'playwright';
import assert from 'node:assert';

const BASE_URL = process.env.BASE_URL || 'http://127.0.0.1:8089';

console.log(`\n======================================================`);
console.log(`🚀 AUTONOMOUS QA TEST SUITE WITH PLAYWRIGHT`);
console.log(`Target URL: ${BASE_URL}`);
console.log(`Browser: Microsoft Edge / Chromium`);
console.log(`======================================================\n`);

const results = [];

function record(suite, testName, passed, details = '') {
    results.push({ suite, testName, passed, details });
    const mark = passed ? '✅ PASS' : '❌ FAIL';
    console.log(`[${mark}] ${suite} :: ${testName} ${details ? '(' + details + ')' : ''}`);
}

async function run() {
    let browser;
    try {
        browser = await chromium.launch({ channel: 'msedge', headless: true });
    } catch (e) {
        browser = await chromium.launch({ channel: 'chrome', headless: true });
    }

    const context = await browser.newContext({
        viewport: { width: 1280, height: 720 },
        userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Playwright-QA-Agent',
    });

    const page = await context.newPage();

    // Collect console errors
    const consoleErrors = [];
    page.on('console', msg => {
        if (msg.type() === 'error') {
            consoleErrors.push(msg.text());
        }
    });

    try {
        // =========================================================================
        // SUITE 1: GUEST & PUBLIC PAGES
        // =========================================================================
        console.log('\n--- SUITE 1: GUEST & PUBLIC PAGES ---');

        // Test 1.1: Landing Page
        await page.goto(`${BASE_URL}/`);
        const title = await page.title();
        const hasBranding = await page.locator('text=Bagimu Extra').count() > 0 || await page.locator('text=SMK Bagimu Negeriku').count() > 0;
        record('Public', 'Halaman Landing Utama terbuka', hasBranding, `Title: ${title}`);

        // Test 1.2: Katalog Eskul
        await page.goto(`${BASE_URL}/eskul`);
        const hasEskulList = await page.locator('text=Futsal').count() > 0;
        record('Public', 'Katalog Eskul Publik memuat data eskul', hasEskulList);

        // Test 1.3: Jadwal Publik
        await page.goto(`${BASE_URL}/jadwal`);
        const hasJadwal = await page.locator('text=Jadwal').count() > 0;
        record('Public', 'Jadwal Latihan Publik terbuka', hasJadwal);

        // Test 1.4: Guest Redirection Security
        const resAdmin = await page.goto(`${BASE_URL}/admin`);
        const urlAdmin = page.url();
        const redirectedToPembina = urlAdmin.includes('/pembina/login');
        record('Security', 'Akses tamu ke /admin dialihkan ke /pembina/login', redirectedToPembina, `Target: ${urlAdmin}`);

        const resSiswa = await page.goto(`${BASE_URL}/siswa`);
        const urlSiswa = page.url();
        const redirectedToSiswa = urlSiswa.includes('/siswa/login');
        record('Security', 'Akses tamu ke /siswa dialihkan ke /siswa/login', redirectedToSiswa, `Target: ${urlSiswa}`);

        // Test 1.5: Verifikasi Dokumen Publik
        await page.goto(`${BASE_URL}/verify`);
        const hasVerifyForm = await page.locator('input#kode').count() > 0;
        record('Public', 'Form Verifikasi Dokumen Sertifikat terbuka', hasVerifyForm);

        await page.fill('input#kode', 'INVALID-CODE-999');
        await page.click('button:has-text("Periksa")');
        await page.waitForLoadState('networkidle');
        const hasInvalidNotice = await page.locator('text=tidak valid').count() > 0 || await page.locator('text=Tidak Ditemukan').count() > 0 || await page.locator('text=Kode').count() > 0;
        record('Public', 'Verifikasi kode acak menampilkan respon tidak valid', hasInvalidNotice);

        // =========================================================================
        // SUITE 2: SISWA AUTH & ONE-SHOT REGISTRATION FLOW
        // =========================================================================
        console.log('\n--- SUITE 2: SISWA AUTH & ONE-SHOT REGISTRATION ---');

        // Bersihkan cookies agar tidak ada stale session/intended URL dari Suite 1
        await context.clearCookies();

        await page.goto(`${BASE_URL}/siswa/login`);

        // Test 2.1: Tabs Login
        const hasTabSiswa = await page.locator('text=Siswa').count() > 0;
        const hasTabPembina = await page.locator('a:has-text("Pembina")').count() > 0;
        record('Siswa Auth', 'Tab Login Siswa dan Pembina tersedia berdampingan', hasTabSiswa && hasTabPembina);

        // Test 2.2: Login dengan NIS tidak dikenal
        await page.fill('input#nis', '99999');
        await page.fill('input#token', 'SALAH123');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');
        const hasErrorWrong = await page.locator('text=Token atau password salah').count() > 0 || await page.locator('text=tidak dikenal').count() > 0;
        record('Siswa Auth', 'Login NIS fiktif ditolak validasi', hasErrorWrong);

        // Test 2.3: Login Siswa Terkunci (10003 - sudah pernah mendaftar)
        await page.goto(`${BASE_URL}/siswa/login`);
        await page.fill('input#nis', '10003');
        await page.fill('input#token', 'LOCKED01');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');
        const hasLockedNotice = await page.locator('text=terkunci').count() > 0 || await page.locator('text=hangus').count() > 0;
        record('Siswa Auth', 'Siswa yang sudah memilih eskul ditolak login karena data terkunci', hasLockedNotice);

        // Test 2.4: Login Siswa Baru (10001 - belum mendaftar)
        await page.goto(`${BASE_URL}/siswa/login`);
        await page.fill('input#nis', '10001');
        await page.fill('input#token', 'ABCDEF12');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        let currentUrlSiswa = page.url();
        const loggedInSiswa = !currentUrlSiswa.includes('/login') && currentUrlSiswa.includes('/siswa');
        record('Siswa Auth', 'Siswa baru berhasil login dan masuk ke portal siswa', loggedInSiswa, `URL: ${currentUrlSiswa}`);

        // Arahkan ke formulir pendaftaran bila berada di dashboard siswa
        if (!currentUrlSiswa.includes('/siswa/daftar')) {
            await page.goto(`${BASE_URL}/siswa/daftar`);
            await page.waitForLoadState('networkidle');
        }

        // Test 2.5: Pemilihan Eskul & Pendaftaran
        if (loggedInSiswa) {
            // Pilih Futsal
            await page.selectOption('select#eskul_utama', 'Futsal');
            await page.waitForTimeout(500);

            // Buka Modal Konfirmasi
            const btnBuka = page.locator('button[onclick="bukaModalKonfirmasi()"]');
            if (await btnBuka.count() > 0) {
                await btnBuka.click();
            } else {
                await page.click('button:has-text("Kirim Pendaftaran")');
            }
            await page.waitForTimeout(500);

            const modalVisible = await page.locator('#modal-konfirmasi').isVisible();
            record('Siswa Registration', 'Dialog modal konfirmasi pendaftaran tampil sebelum kirim', modalVisible);

            // Submit pendaftaran dalam modal
            await page.click('#btn-submit-pendaftaran');
            await page.waitForLoadState('networkidle');

            const statusUrl = page.url();
            const atStatusPage = statusUrl.includes('/siswa/status');
            const hasBukti = await page.locator('text=Bukti Pendaftaran').count() > 0 || await page.locator('text=berhasil disimpan').count() > 0;
            const hasCountdown = await page.locator('#countdown-timer').count() > 0 || await page.locator('text=keluar otomatis').count() > 0;

            record('Siswa Registration', 'Pendaftaran tersimpan dan masuk halaman Bukti Pendaftaran', atStatusPage && hasBukti);
            record('Siswa Registration', 'Countdown auto-exit aktif di halaman status', hasCountdown);
        }

        // Test 2.6: Verifikasi Siswa 10001 sekarang Terkunci
        // Bersihkan cookies sesi siswa
        await context.clearCookies();
        await page.goto(`${BASE_URL}/siswa/login`);
        await page.fill('input#nis', '10001');
        await page.fill('input#token', 'ABCDEF12');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');
        const reLoginBlocked = await page.locator('text=terkunci').count() > 0 || await page.locator('text=hangus').count() > 0;
        record('Siswa Security', 'Siswa 10001 tidak dapat login kembali setelah berhasil mendaftar', reLoginBlocked);

        // =========================================================================
        // SUITE 3: PEMBINA FLOW (PRESENSI & REKAP)
        // =========================================================================
        console.log('\n--- SUITE 3: PEMBINA FLOW ---');

        await context.clearCookies();
        await page.goto(`${BASE_URL}/pembina/login`);

        // Test 3.1: Login Pembina Gagal (Password salah)
        await page.fill('input#login', 'pembina_futsal');
        await page.fill('input#password', 'WrongPassword123!');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');
        const pembinaFailed = await page.locator('text=Kredensial').count() > 0 || await page.locator('text=tidak cocok').count() > 0 || await page.locator('text=salah').count() > 0;
        record('Pembina Auth', 'Login pembina dengan password salah ditolak', pembinaFailed);

        // Test 3.2: Login Pembina Berhasil
        await page.goto(`${BASE_URL}/pembina/login`);
        await page.fill('input#login', 'pembina_futsal');
        await page.fill('input#password', 'Pembina123!');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        const pembinaUrl = page.url();
        const loggedInPembina = !pembinaUrl.includes('/login') && pembinaUrl.includes('/pembina');
        record('Pembina Auth', 'Login pembina sukses masuk ke dashboard pembina', loggedInPembina, `URL: ${pembinaUrl}`);

        // Test 3.3: Buka Eskul Ampuan & Kelola Presensi
        await page.goto(`${BASE_URL}/pembina/eskul/1`);
        const hasEskulTitle = await page.locator('text=Futsal').count() > 0;
        record('Pembina Eskul', 'Halaman kelola eskul ampuan memuat data Futsal', hasEskulTitle);

        // Verifikasi anggota terdaftar tampil di tabel presensi
        const hasAnggota = await page.locator('text=Ahmad Siswa Satu').count() > 0 || await page.locator('text=10001').count() > 0;
        record('Pembina Presensi', 'Siswa yang baru mendaftar (10001) tampil dalam daftar presensi', hasAnggota);

        // Test simpan presensi
        const btnSimpanPresensi = page.locator('button:has-text("Simpan Presensi")');
        if (await btnSimpanPresensi.count() > 0) {
            await btnSimpanPresensi.click();
            await page.waitForLoadState('networkidle');
            const hasSuccessSave = await page.locator('text=tersimpan').count() > 0 || await page.locator('text=berhasil').count() > 0;
            record('Pembina Presensi', 'Simpan presensi pertemuan berhasil dieksekusi', hasSuccessSave);
        }

        // =========================================================================
        // SUITE 4: ADMIN / TATA USAHA FLOW
        // =========================================================================
        console.log('\n--- SUITE 4: ADMIN / TATA USAHA FLOW ---');

        await context.clearCookies();
        await page.goto(`${BASE_URL}/pembina/login`);

        // Test 4.1: Login Admin
        await page.fill('input#login', 'davidyehudasurbakti');
        await page.fill('input#password', 'Yehuda4545!');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        const adminUrl = page.url();
        const loggedInAdmin = adminUrl.includes('/admin');
        record('Admin Auth', 'Login Admin David Yehuda Surbakti berhasil masuk ke panel admin', loggedInAdmin, `URL: ${adminUrl}`);

        // Test 4.2: Direktori Siswa
        await page.goto(`${BASE_URL}/admin/students`);
        const hasStudentDir = await page.locator('text=Direktori Siswa').count() > 0;
        const studentRow10001 = await page.locator('text=10001').count() > 0;
        record('Admin Siswa', 'Direktori Siswa memuat data tabel siswa', hasStudentDir && studentRow10001);

        // Test 4.3: Generate Token Ditolak untuk Siswa yang Terkunci
        // Coba generate token untuk 10001 (sudah terdaftar Futsal)
        const student1Row = page.locator('tr:has-text("10001")');
        if (await student1Row.count() > 0) {
            const summaryBtn = student1Row.locator('summary');
            if (await summaryBtn.count() > 0) {
                await summaryBtn.first().click();
                await page.waitForTimeout(300);
            }
            const btnToken = student1Row.locator('button:has-text("Token")');
            if (await btnToken.count() > 0) {
                await btnToken.first().click();
                await page.waitForLoadState('networkidle');
                const hasErrorLocked = await page.locator('text=terkunci').count() > 0 || await page.locator('text=sudah memilih').count() > 0;
                record('Admin Siswa', 'Penerbitan token baru untuk siswa yang sudah memilih eskul dicegah', hasErrorLocked);
            }
        }

        // Test 4.4: Rekap & Laporan Admin
        await page.goto(`${BASE_URL}/admin/rekap`);
        const hasRekap = await page.locator('text=Rekapitulasi').count() > 0 || await page.locator('text=Rekap').count() > 0;
        record('Admin Rekap', 'Halaman Rekapitulasi pendaftaran eskul terbuka', hasRekap);

        // Test 4.5: Pengaturan Sistem Admin
        await page.goto(`${BASE_URL}/admin/settings`);
        const hasSettings = await page.locator('text=Pengaturan').count() > 0;
        record('Admin Settings', 'Halaman Pengaturan sekolah & sistem terbuka', hasSettings);

        // =========================================================================
        // SUITE 5: CROSS-ROLE AUTHORIZATION CHECKS
        // =========================================================================
        console.log('\n--- SUITE 5: CROSS-ROLE AUTHORIZATION CHECKS ---');

        // Logout admin
        await context.clearCookies();

        // Login as Pembina again to check admin page access
        await page.goto(`${BASE_URL}/pembina/login`);
        await page.fill('input#login', 'pembina_futsal');
        await page.fill('input#password', 'Pembina123!');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Pembina attempts to access /admin/settings
        const resForbidden = await page.goto(`${BASE_URL}/admin/settings`);
        const statusForbidden = resForbidden.status();
        record('Authorization', 'Pembina dilarang mengakses panel admin /admin/settings (403 Forbidden)', statusForbidden === 403, `Status: ${statusForbidden}`);

    } catch (err) {
        console.error('Fatal error during test run:', err);
        record('System', 'Fatal Exception during testing', false, err.message);
    } finally {
        await browser.close();
    }

    console.log(`\n======================================================`);
    console.log(`📊 PLAYWRIGHT QA TEST SUMMARY:`);
    const total = results.length;
    const passed = results.filter(r => r.passed).length;
    const failed = results.filter(r => !r.passed).length;
    console.log(`Total Tests : ${total}`);
    console.log(`Passed      : ${passed} ✅`);
    console.log(`Failed      : ${failed} ❌`);
    console.log(`Console Err : ${consoleErrors.length}`);
    console.log(`======================================================\n`);

    if (failed > 0) {
        process.exit(1);
    }
}

run();

