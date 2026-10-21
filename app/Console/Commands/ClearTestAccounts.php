<?php

namespace App\Console\Commands;

use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;

class ClearTestAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-accounts:clear {--force : Paksa hapus tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus semua data akun uji (admin, pembina, siswa, wali kelas test)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! app()->environment('local', 'staging', 'testing')) {
            $this->error('Perintah ini hanya boleh dijalankan pada lingkungan local/staging!');
            return Command::FAILURE;
        }

        $userIds = User::where('email', 'like', '%@test.local')
            ->orWhere('username', 'like', 'uji_%')
            ->orWhere('username', 'like', 'TEST%')
            ->pluck('id');

        $count = $userIds->count();

        if ($count === 0) {
            $this->info('Tidak ditemukan akun uji yang perlu dibersihkan.');
            return Command::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Ditemukan {$count} akun uji. Lanjutkan pembersihan?")) {
            $this->info('Pembersihan dibatalkan.');
            return Command::SUCCESS;
        }

        Registration::whereIn('user_id', $userIds)
            ->orWhere('nis', 'like', 'TEST%')
            ->delete();

        Student::where('nis', 'like', 'TEST%')
            ->orWhereIn('user_id', $userIds)
            ->delete();

        User::whereIn('id', $userIds)->delete();

        $this->info("Berhasil membersihkan {$count} akun uji berserta data terkait.");

        return Command::SUCCESS;
    }
}
